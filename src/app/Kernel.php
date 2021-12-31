<?php
declare(strict_types=1);

namespace DaWaPack;

use DaWaPack\Chassis\Classes\Base\KernelBase;
use DaWaPack\Chassis\Helpers\Pcntl\PcntlSignals;
use DaWaPack\Classes\Threads\ThreadsManager;
use DaWaPack\Interfaces\ThreadsManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Kernel extends KernelBase
{

    private const DAEMON_LOOP_EACH_MS = 100; // 100ms - max 10q loops / sec
    private const WORKER_LOOP_EACH_MS = 100; // 100ms - max 10 loops / sec
    private bool $stopRequested = false;

    /**
     * @inheritDoc
     */
    public function boot(?string $threadId = null): void
    {
        if (defined('RUNNER_TYPE') && RUNNER_TYPE === "worker") {
            $this->bootWorker($threadId);
            return;
        }
        $this->bootDaemon();
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
                "extra" => ["signal" => PcntlSignals::$toSignalName[$signalNumber]]
            ]
        );
        $this->stopRequested = true;
    }

    /**
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function bootDaemon(): void
    {
        // Is daemon type, so start threads manager
        /** @var ThreadsManager $threadsManager */
        $threadsManager = $this->app->get(ThreadsManagerInterface::class);
        $threadsManager->spawnThreads();
        do {
            $startAt = microtime(true);

            // TODO: implements thread event pool, vertical scaling, respawn, despawn, and so on

            // Mostly we wait DAEMON_LOOP_EACH_MS
            $this->loopWait(self::DAEMON_LOOP_EACH_MS, $startAt);
        } while (!$this->stopRequested);
        // Gracefully stop all threads
        $threadsManager->exit();
    }

    /**
     * @param string|null $threadId
     *
     * @return void
     */
    protected function bootWorker(?string $threadId = null): void
    {
        // check logger is working
        $this->logger()->info(
            "thread worker spawned",
            ["component" => $this->loggerComponent, "threadId" => $threadId]
        );

        // TODO: implements broker consumer

        // TO BE REMOVED AFTER BROKER CONSUMER IMPLEMENTATION
        do {
            $startAt = microtime(true);
            // Mostly we wait DAEMON_LOOP_EACH_MS
            $this->loopWait(self::WORKER_LOOP_EACH_MS, $startAt);
        } while (!$this->stopRequested);
    }

    /**
     * @param int $loopEach
     * @param float $startAt
     *
     * @return void
     */
    private function loopWait(int $loopEach, float $startAt): void
    {
        $loopWait = $loopEach - (round((microtime(true) - $startAt) * 1000));
        if ($loopWait > 0) {
            usleep(((int)$loopWait * 1000));
        }
    }
}
