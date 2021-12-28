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

    private string $basePath;

    private string $runnerType;

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
        $this->enableAutoWiring();
        $this->bootstrapContainer();
        $this->registerErrorHandling();
        $this->registerRunnerType();

        if (!$this->runningInConsole()) {
            trigger_error("Run only in cli mode", E_USER_ERROR);
        }

        // pcntl signals must be async
        !pcntl_async_signals() && pcntl_async_signals(true);
    }

    /**
     * Check and register runner type
     *
     * @return void
     */
    private function registerRunnerType(): void
    {
        !defined('RUNNER_TYPE') && trigger_error(
            "boot script must define the runner type",
            E_USER_ERROR
        );
        $this->runnerType = RUNNER_TYPE;
        !$this->isValidRunner() && trigger_error(
            "unknown runner type",
            E_USER_ERROR
        );
    }

    /**
     * @return bool
     */
    public function runningInConsole(): bool
    {
        return \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';
    }

    /**
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function bootstrapContainer(): void
    {
        // Add singletons
        $this->add(LoggerInterface::class, new LoggerFactory($this->basePath));
        $this->add('config', new Configuration(
            new LeagueConfiguration(),
            $this->logger(),
            $this->basePath,
            ['app']
        ));
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
            // fault tolerant - just log the thrown exception & return null
            $this->logger()->error(
                $reason->getMessage(),
                ["error" => $reason]
            );
        }
        return null;
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
     * Instantiate the container auto wiring
     *
     * @return void
     */
    private function enableAutoWiring(): void
    {
        $this->delegate(new ReflectionContainer(true));
    }
}
