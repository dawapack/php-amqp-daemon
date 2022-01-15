<?php
declare(strict_types=1);

namespace DaWaPack;

use DaWaPack\Chassis\Classes\Base\KernelBase;
use DaWaPack\Chassis\Helpers\Pcntl\PcntlSignals;
use DaWaPack\Classes\Threads\ThreadsManagerInterface;
use DaWaPack\Classes\Workers\WorkerInterface;

class Kernel extends KernelBase
{

    private bool $stopRequested = false;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (RUNNER_TYPE === "worker") {
            ($this->app->get(WorkerInterface::class))->start();
        } elseif (RUNNER_TYPE === "daemon") {
            ($this->app->get(ThreadsManagerInterface::class))->start($this->stopRequested);
        }
    }

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
    public function signalHandler(int $signalNumber, $signalInfo): void
    {
        $this->stopRequested = true;
        if ($signalNumber === PcntlSignals::SIGTERM) {
            return;
        }
        $this->logger()->alert(
            "exit on trapped signal",
            [
                "component" => $this->loggerComponent,
                "extra" => ["signal" => PcntlSignals::$toSignalName[$signalNumber]]
            ]
        );
    }
}
