<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Worker;

use DaWaPack\Chassis\Classes\Base\KernelBase;
use DaWaPack\Chassis\Helpers\Pcntl\PcntlSignals;

class Kernel extends KernelBase
{

    /**
     * @var bool
     */
    private bool $hangupRequested = false;

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
        if ($signalNumber === PcntlSignals::SIGHUP) {
            $this->hangupRequested = true;
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
        do {
            echo "Ctrl+C to STOP..." . PHP_EOL;
            sleep(30);
        } while (!$this->hangupRequested);
    }
}
