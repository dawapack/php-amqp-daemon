<?php

namespace DaWaPack\Classes\Daemon;

use DaWaPack\Chassis\Classes\Base\KernelBase;
use DaWaPack\Chassis\Helpers\Pcntl\PcntlSignals;

class Kernel extends KernelBase
{

    /**
     * @var bool
     */
    private bool $stopRequested = false;

    /**
     * @inheritDoc
     */
    protected function bootstrap(): void
    {
        $this->logger()->info("kernel bootstrapped", ['component' => $this->loggerComponent]);
        $this->bootstrapSignals();
    }

    /**
     * @param int $signalNumber
     * @param $signalInfo
     */
    protected function signalHandler(int $signalNumber, $signalInfo): void
    {
        if ($signalNumber === PcntlSignals::SIGTERM) {
            $this->stopRequested = true;
            return;
        }
        $this->logger()->alert(
            "exit on trapped signal",
            [
                "component" => $this->loggerComponent,
                "extra" => [
                    "signal" => PcntlSignals::$toSignalName[$signalNumber]
                ]
            ]
        );
        exit($signalNumber);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        // Start threads

        // Loop until stop requested
        do {
            // Run scaling

            // Wait a while - prevent CPU load
            usleep(50000);
        } while (!$this->stopRequested);

        // Stop all threads
    }
}
