<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Threads;

use DaWaPack\Classes\Threads\Configuration\ThreadConfiguration;
use DaWaPack\Interfaces\ThreadInstanceInterface;
use DaWaPack\Interfaces\ThreadsConfigurationInterface;
use DaWaPack\Interfaces\ThreadsManagerInterface;
use parallel\Future;
use function DaWaPack\Chassis\Helpers\app;

class ThreadsManager implements ThreadsManagerInterface
{

    private ThreadsConfigurationInterface $threadsConfiguration;
    private array $threads = [];
    private array $channels = [];

    /**
     * ThreadsManager constructor.
     *
     * @param ThreadsConfigurationInterface $threadsConfiguration
     */
    public function __construct(
        ThreadsConfigurationInterface $threadsConfiguration
    ) {
        $this->threadsConfiguration = $threadsConfiguration;
    }

    /**
     * @param bool $wait
     *
     * @return void
     */
    public function exit(bool $wait = true): void
    {
        $futuresToCancel = [];
        /**
         * @var string $threadId
         * @var ThreadInstance $threadInstance
         */
        foreach ($this->threads as $threadId => $threadInstance) {
            $futuresToCancel[$threadId] = $threadInstance->getFuture();
            $futuresToCancel[$threadId]->cancel();
        }
        if (!$wait) {
            return;
        }
        // Wait for all threads to stop
        do {
            $allCanceled = array_reduce(
                $futuresToCancel,
                function (bool $c, Future $future): bool {
                    return $c && $future->done();
                },
                true
            );
            // Wait a while - prevent CPU load
            usleep(250000);
        } while (false === $allCanceled);
    }

    /**
     *
     * @return void
     */
    public function spawnThreads(): void
    {
        // Spawn infrastructure thread
        $this->threadsConfiguration->hasInfrastructureThread() && $this->spawnThread(
            $this->threadsConfiguration->getThreadConfiguration('infrastructure')
        );
        // Spawn centralized configuration thread
        $this->threadsConfiguration->hasCentralizedConfigurationThread() && $this->spawnThread(
            $this->threadsConfiguration->getThreadConfiguration('configuration')
        );
        // Spawn worker threads
        $workersConfiguration = $this->threadsConfiguration->getThreadConfiguration('worker');
        foreach ($workersConfiguration->channels as $channel) {
            $this->spawnThread(new ThreadConfiguration($channel));
        }
    }

    /**
     * @param ThreadConfiguration $threadConfiguration
     *
     * @return void
     */
    private function spawnThread(ThreadConfiguration $threadConfiguration): void
    {
        for ($threadCnt = 0; $threadCnt < $threadConfiguration->minimum; $threadCnt++) {
            /** @var ThreadInstance $threadInstance */
            $threadInstance = app(ThreadInstanceInterface::class);
            $threadInstance->setConfiguration($threadConfiguration);
            // Spawn thread - retrieve a UUID as reference
            $threadId = $threadInstance->spawn();
            $this->threads[$threadId] = $threadInstance;
            if (!isset($this->channels[$threadConfiguration->channelName])) {
                $this->channels[$threadConfiguration->channelName] = [];
            }
            $this->channels[$threadConfiguration->channelName][$threadId] = null;
        }
    }
}
