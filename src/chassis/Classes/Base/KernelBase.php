<?php

namespace DaWaPack\Chassis\Classes\Base;

use DaWaPack\Chassis\Application;
use DaWaPack\Chassis\Helpers\Pcntl\PcntlSignals;
use Psr\Log\LoggerInterface;

abstract class KernelBase implements KernelInterface
{

    /**
     * @var string
     */
    protected string $loggerComponent = "application_" . RUNNER_TYPE;

    /**
     * @var Application
     */
    protected Application $app;

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

    /**
     * @return void
     */
    abstract protected function bootstrap(): void;

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
        return $this->app->get(LoggerInterface::class);
    }

    final protected function bootstrapSignals(): void
    {
        pcntl_signal(PcntlSignals::SIGHUP, array($this, 'signalHandler'));
        pcntl_signal(PcntlSignals::SIGTERM, array($this, 'signalHandler'));
        pcntl_signal(PcntlSignals::SIGINT, array($this, 'signalHandler'));
        pcntl_signal(PcntlSignals::SIGPWR, array($this, 'signalHandler'));
    }
}
