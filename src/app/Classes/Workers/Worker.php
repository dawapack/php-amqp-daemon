<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Workers;

use DaWaPack\Classes\Messages\InterProcessCommunication;
use parallel\Events;
use parallel\Events\Event;
use parallel\Events\Event\Type as EventType;
use Throwable;
use function DaWaPack\Chassis\Helpers\app;

class Worker implements WorkerInterface
{

    private const LOGGER_COMPONENT_PREFIX = "worker_";
    private const EVENTS_POOL_TIMEOUT_MS = 0.1;
    private const LOOP_EACH_MS = 100;

    private Events $events;

    /**
     * @return void
     */
    public function start(): void
    {
        $this->events = new Events();
        $this->eventsSetTimeout(self::EVENTS_POOL_TIMEOUT_MS);
        $this->events->addChannel(app("incomingChannel"));
        $startAt = microtime(true);
        do {
            // wait for threads event
            if (!$this->eventsPoll()) {
                break;
            }
            $this->loopWait(self::LOOP_EACH_MS, $startAt);
            $startAt = microtime(true);
        } while (true);
    }

    /**
     * @return bool
     */
    protected function eventsPoll(): bool
    {
        try {
            do {
                // Poll for event from threads
                $event = $this->events->poll();
                if (is_null($event)) {
                    break;
                }
                return $this->eventHandler($event);
            } while (true);
        } catch (Throwable $reason) {
            // fault-tolerant - nothing to do
        }

        return true;
    }

    /**
     * @param Event $event
     *
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function eventHandler(Event $event): bool
    {
        switch ($event->type) {
            case EventType::Read:
                if ((new InterProcessCommunication(app("outgoingChannel"), $event))->handle()->isAborting()) {
                    return false;
                }
                $this->events->addChannel(app("incomingChannel"));
                break;
            default:
                app()->logger()->warning(
                    "get unhandled event",
                    ["component" => self::LOGGER_COMPONENT_PREFIX . "event_handler", "extra" => (array)$event]
                );
                break;
        }

        return true;
    }

    /**
     * @param float $timeout
     *
     * @return void
     */
    protected function eventsSetTimeout(float $timeout): void
    {
        // timeout must be in microseconds
        $timeout = (int)($timeout * 1000);
        $this->events->setBlocking(true);
        $this->events->setTimeout($timeout);
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
