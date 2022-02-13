<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework;

use DaWaPack\Chassis\Application;
use DaWaPack\Chassis\Helpers\Pcntl\PcntlSignals;
use Psr\Log\LoggerInterface;

abstract class KernelBase implements KernelInterface
{
    protected Application $app;
    protected string $loggerComponent = "kernel_" . RUNNER_TYPE;

    /**
     * KernelBase constructor.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
        $this->bootstrap();
    }

    abstract protected function bootstrap(): void;
    abstract protected function signalHandler(int $signalNumber, $signalInfo): void;

    /**
     * @inheritDoc
     */
    final public function app(): Application
    {
        return $this->app;
    }

    /**
     * @inheritDoc
     */
    final public function logger(): LoggerInterface
    {
        return $this->app->logger();
    }

    final protected function bootstrapSignals(): void
    {
        pcntl_signal(PcntlSignals::SIGTERM, array($this, 'signalHandler'));
    }
}
