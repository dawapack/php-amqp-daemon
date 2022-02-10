<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Workers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Streamers\InfrastructureStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Classes\Messages\InterProcessCommunication;
use DaWaPack\Classes\Threads\Exceptions\ThreadConfigurationException;
use parallel\Events;
use parallel\Events\Event;
use parallel\Events\Event\Type as EventType;
use Psr\Log\LoggerInterface;
use Throwable;

use function DaWaPack\Chassis\Helpers\app;
use function DaWaPack\Chassis\Helpers\subscribe;

class Worker implements WorkerInterface
{
    private const LOGGER_COMPONENT_PREFIX = "worker_";
    private const EVENTS_POOL_TIMEOUT_MS = 1;
    private const LOOP_EACH_MS = 50;

    private Events $events;
    private SubscriberStreamer $subscriberStreamer;

    /**
     * @return void
     */
    public function start(): void
    {
        try {
            $this->eventsSetup();
            $this->subscriberSetup();
            $startAt = microtime(true);
            do {
                // wait for threads event
                if (!$this->eventsPoll()) {
                    break;
                }
                // iterate broker consumer
                $this->subscriberPoll();
                // wait a while - prevent CPU load
                $this->loopWait(self::LOOP_EACH_MS, $startAt);
                $startAt = microtime(true);
            } while (true);
        } catch (Throwable $reason) {
            // log this error & request respawning
            app()->logger()->error(
                $reason->getMessage(),
                [
                    'component' => self::LOGGER_COMPONENT_PREFIX . "error",
                    'error' => $reason
                ]
            );
            (new InterProcessCommunication(app("outgoingChannel")))
                ->setMessage("respawn")
                ->send();
        }
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function eventsSetup(): void
    {
        $this->events = new Events();
        $this->events->setBlocking(true);
        $this->events->setTimeout(self::EVENTS_POOL_TIMEOUT_MS);
        $this->events->addChannel(app("incomingChannel"));
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function subscriberSetup(): void
    {
        $threadConfiguration = app('threadConfiguration');
        switch ($threadConfiguration["threadType"]) {
            case "infrastructure":
                // Broker channels setup
                (new InfrastructureStreamer(
                    app()->get('brokerStreamConnection'),
                    new ContractsManager(
                        app(BrokerConfigurationInterface::class),
                        new ContractsValidator()
                    ),
                    app(LoggerInterface::class)
                ))->brokerChannelsSetup();
                break;
            case "configuration":
                // wait after infrastructure to finish exchange/queues/bindings declarations
                sleep(5);
                // TODO: implement configuration listener - (centralized configuration server feature)
                break;
            case "worker":
                // wait after infrastructure to finish exchange/queues/bindings declarations
                sleep(5);
                // create subscriber
                $this->subscriberStreamer = subscribe(
                    $threadConfiguration["channelName"],
                    $threadConfiguration["handler"]
                );
                break;
            default:
                throw new ThreadConfigurationException("unknown thread type");
        }
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
     * @return void
     */
    protected function subscriberPoll(): void
    {
        if (!isset($this->subscriberStreamer)) {
            return;
        }
        $this->subscriberStreamer->iterate();
        if ($this->subscriberStreamer->consumed()) {
            $messageBag = $this->subscriberStreamer->get();

            // TODO: implement event listener - application message handler
            app()->logger()->info(
                "new message bag received",
                [
                    "component" => self::LOGGER_COMPONENT_PREFIX . "info",
                    "message_type" => $messageBag->getProperty("type")
                ]
            );
        }
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
