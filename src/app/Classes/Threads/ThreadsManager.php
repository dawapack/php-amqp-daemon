<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Threads;

use DaWaPack\Classes\Messages\InterProcessCommunication;
use DaWaPack\Classes\Threads\Configuration\ThreadConfiguration;
use DaWaPack\Classes\Threads\Configuration\ThreadsConfigurationInterface;
use parallel\Events;
use parallel\Events\Error\Timeout;
use parallel\Events\Event;
use parallel\Events\Event\Type as EventType;
use function DaWaPack\Chassis\Helpers\app;

class ThreadsManager implements ThreadsManagerInterface
{

    private const LOGGER_COMPONENT_PREFIX = "thread_manager_";
    private const EVENTS_POOL_TIMEOUT_MS = 1;
    private const LOOP_EACH_MS = 100;

    private ThreadsConfigurationInterface $threadsConfiguration;
    private Events $events;
    private array $threads = [];
    private array $channels = [];

    /**
     * ThreadsManager constructor.
     *
     * @param ThreadsConfigurationInterface $threadsConfiguration
     * @param Events $events
     */
    public function __construct(ThreadsConfigurationInterface $threadsConfiguration, Events $events)
    {
        $this->threadsConfiguration = $threadsConfiguration;
        $this->events = $events;
    }

    /**
     * @param bool $stopRequested
     *
     * @return void
     */
    public function start(bool &$stopRequested): void
    {
        $this->threadsSetup();
        $this->eventsSetup();

        $startAt = microtime(true);
        do {
            if ($stopRequested) {
                $this->stop();
                break;
            }
            // wait for threads event
            $this->eventsPoll();
            // Wait a while - prevent CPU load
            $this->loopWait(self::LOOP_EACH_MS, $startAt);
            $startAt = microtime(true);
        } while (true);

    }

    /**
     * @return void
     */
    protected function stop(): void
    {
        /**
         * @var ThreadInstance $threadInstance
         */
        foreach ($this->threads as $threadInstance) {
            (new InterProcessCommunication($threadInstance->getIncomingChannel(), null))
                ->setMessage("abort")
                ->send();
        }

        $startAt = microtime(true);
        do {
            // wait for threads event
            $this->eventsPoll();
            // Wait a while - prevent CPU load
            $this->loopWait(self::LOOP_EACH_MS, $startAt);
            $startAt = microtime(true);
        } while (!empty($this->threads));
    }

    /**
     * @return void
     */
    protected function eventsPoll(): void
    {
        try {
            do {
                // Poll for events from threads
                $event = $this->events->poll();
                if (is_null($event)) {
                    break;
                }
                $this->eventHandler($event);
            } while (true);
        } catch (Timeout $reason) {
            // fault-tolerant - nothing to do
        }
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    protected function eventHandler(Event $event): void
    {
        switch ($event->type) {
            case EventType::Read:
                $threadId = str_replace(array("-out", "-in"), "", $event->source);
                $channel = $this->threads[$threadId]->getOutgoingChannel();
                $ipc = (new InterProcessCommunication($channel, $event))->handle();
                if ($ipc->isRespawnRequested()) {
                    $this->respawnThread($this->threads[$threadId]->getConfiguration());
                }
                if ($ipc->isAborting() || $ipc->isRespawnRequested()) {
                    unset($this->threads[$threadId]);
                    unset($this->channels[$threadId]);
                    break;
                }
                $this->events->addChannel($channel);
                break;
            default:
                app()->logger()->warning(
                    "get unhandled event",
                    ["component" => self::LOGGER_COMPONENT_PREFIX . "event_handler", "extra" => (array)$event]
                );
                break;
        }
    }

    /**
     * @return void
     */
    public function threadsSetup(): void
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
        if ($workersConfiguration->enabled) {
            foreach ($workersConfiguration->channels as $channel) {
                $this->spawnThread(new ThreadConfiguration($channel));
            }
        }
    }

    /**
     * @param ThreadConfiguration $threadConfiguration
     *
     * @return void
     */
    protected function spawnThread(ThreadConfiguration $threadConfiguration): void
    {
        for ($threadCnt = 0; $threadCnt < $threadConfiguration->minimum; $threadCnt++) {
            /** @var ThreadInstance $threadInstance */
            $threadInstance = app(ThreadInstanceInterface::class);
            $threadInstance->setConfiguration($threadConfiguration);
            // Spawn thread
            $this->createAndStackNewThread($threadInstance, $threadConfiguration);
        }
    }

    protected function respawnThread(array $configuration)
    {
        $threadConfiguration = new ThreadConfiguration($configuration);
        /** @var ThreadInstance $threadInstance */
        $threadInstance = app(ThreadInstanceInterface::class);
        $threadInstance->setConfiguration($threadConfiguration);
        // Spawn thread
        $this->createAndStackNewThread($threadInstance, $threadConfiguration);

    }

    protected function createAndStackNewThread(
        ThreadInstance $threadInstance,
        ThreadConfiguration $threadConfiguration
    ) {
        $threadId = $threadInstance->spawn();
        $this->threads[$threadId] = $threadInstance;
        if (!isset($this->channels[$threadConfiguration->channelName])) {
            $this->channels[$threadConfiguration->channelName] = [];
        }
        $this->channels[$threadConfiguration->channelName][$threadId] = null;
        $this->events->addChannel($threadInstance->getOutgoingChannel());
    }

    /**
     * @return void
     */
    protected function eventsSetup(): void
    {
        // timeout must be in microseconds
        $this->events->setBlocking(true);
        $this->events->setTimeout(self::EVENTS_POOL_TIMEOUT_MS);
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
