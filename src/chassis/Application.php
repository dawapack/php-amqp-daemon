<?php

namespace DaWaPack\Chassis;

use DaWaPack\Chassis\Concerns\ErrorsHandler;
use DaWaPack\Chassis\Concerns\Runner;
use Psr\Log\LoggerInterface;
use Throwable;

class Application
{

    /**
     * Traits
     */
    use ErrorsHandler;
    use Runner;

    /**
     * @var string
     */
    private string $basePath;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var string
     */
    private string $runnerType;

    /**
     * Application constructor.
     *
     * @param LoggerInterface $logger
     * @param string|null $basePath
     *
     * @throws Throwable
     */
    public function __construct(LoggerInterface $logger, string $basePath = null)
    {
        $this->logger = $logger;
        $this->basePath = $basePath;

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

    /**
     * @return void
     */
    protected function bootstrapContainer(): void
    {
        // TODO: implement container stuffs here
    }

    /**
     * Set logger interface
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
