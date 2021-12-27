<?php

namespace DaWaPack\Chassis;

use DaWaPack\Chassis\Classes\Logger\LoggerFactory;
use DaWaPack\Chassis\Concerns\ErrorsHandler;
use DaWaPack\Chassis\Concerns\Runner;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Log\LoggerInterface;

class Application extends Container
{

    use ErrorsHandler;
    use Runner;

    private string $basePath;

    private string $runnerType;

    public function __construct(string $basePath = null)
    {
        parent::__construct();
        $this->basePath = $basePath;

        $this->enableAutoWiring();
        $this->bootstrapContainer();
        $this->registerErrorHandling();
        $this->registerRunnerType();

        if (!$this->runningInConsole()) {
            trigger_error("Run only in cli mode", E_USER_ERROR);
        }

        // pcntl signals must be async
        !pcntl_async_signals(null) && pcntl_async_signals(true);
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
    public function runningInConsole()
    {
        return \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';
    }

    protected function bootstrapContainer(): void
    {
        $this->add(LoggerInterface::class, new LoggerFactory($this->basePath));
    }

    private function enableAutoWiring(): void
    {
        $this->delegate(new ReflectionContainer(true));
    }
}
