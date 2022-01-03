<?php
declare(strict_types=1);

namespace DaWaPack\Chassis;

use DaWaPack\Chassis\Classes\Config\Configuration;
use DaWaPack\Chassis\Classes\Logger\LoggerFactory;
use DaWaPack\Chassis\Concerns\ErrorsHandler;
use DaWaPack\Chassis\Concerns\Runner;
use League\Config\Configuration as LeagueConfiguration;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class Application extends Container
{

    use ErrorsHandler;
    use Runner;

    private static Application $instance;
    private string $basePath;

    /**
     * Application constructor.
     *
     * @param string|null $basePath
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(string $basePath = null)
    {
        parent::__construct();
        $this->basePath = $basePath;

        // make all definitions to default to shared - singletons
        $this->defaultToShared(true);
        // use auto-wiring
        $this->enableAutoWiring();
        $this->bootstrapContainer();
        $this->registerErrorHandling();
        $this->registerRunnerType();

        if (!$this->runningInConsole()) {
            trigger_error("Run only in cli mode", E_USER_ERROR);
        }

        // pcntl signals must be async
        pcntl_async_signals(true);

        self::$instance = $this;
    }

    /**
     * @return Application|null
     */
    public static function getInstance(): ?Application
    {
        return isset(self::$instance) && (self::$instance instanceof Application)
            ? self::$instance
            : null;
    }

    /**
     * @return bool
     */
    public function runningInConsole(): bool
    {
        return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
    }

    /**
     * @param string $key
     *
     * @return array|mixed|null
     *
     * @throws Throwable
     */
    public function config(string $key)
    {
        try {
            return $this->get('config')->get($key);
        } catch (Throwable $reason) {
            // fault-tolerant - just log the thrown exception & return null
            $this->logger()->error(
                $reason->getMessage(),
                ["error" => $reason]
            );
        }
        return null;
    }

    /**
     * @param string $alias
     *
     * @return void
     *
     * @throws Throwable
     */
    public function withConfig(string $alias): void
    {
        try {
            $this->get('config')->load($alias);
        } catch (Throwable $reason) {
            // fault-tolerant - just log the thrown exception
            $this->logger()->error(
                $reason->getMessage(),
                ["error" => $reason]
            );
        }
    }

    /**
     * @return LoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function logger(): LoggerInterface
    {
        return $this->get(LoggerInterface::class);
    }

    /**
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function bootstrapContainer(): void
    {
        // Add singletons by his interface
        $this->add(LoggerInterface::class, new LoggerFactory($this->basePath));

        // Add singletons by alias
        $this->add('config', new Configuration(
            new LeagueConfiguration(),
            $this->logger(),
            $this->basePath,
            ['app']
        ));

        // Add paths
        $this->add('basePath', $this->basePath);
        $this->add('configPath', $this->basePath . "/config");
        $this->add('logsPath', $this->basePath . "/logs");
        $this->add('tempPath', $this->basePath . "/tmp");
        $this->add('vendorPath', $this->basePath . "/vendor");
    }

    /**
     * Instantiate the container auto wiring - resolutions will be cached
     *
     * @return void
     */
    private function enableAutoWiring(): void
    {
        $this->delegate(new ReflectionContainer(true));
    }
}
