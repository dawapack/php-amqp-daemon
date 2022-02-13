<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Workers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\InfrastructureStreamer;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Chassis\Framework\InterProcessCommunication\ChannelsInterface;
use DaWaPack\Chassis\Framework\InterProcessCommunication\DataTransferObject\IPCMessage;
use DaWaPack\Chassis\Framework\InterProcessCommunication\ParallelChannels;
use DaWaPack\Chassis\Framework\Routers\Router;
use DaWaPack\Chassis\Framework\Routers\RouterInterface;
use DaWaPack\Chassis\Framework\Threads\Exceptions\ThreadConfigurationException;
use Psr\Log\LoggerInterface;
use Throwable;

use function DaWaPack\Chassis\Helpers\app;
use function DaWaPack\Chassis\Helpers\subscribe;

class Worker implements WorkerInterface
{
    private const LOGGER_COMPONENT_PREFIX = "worker_";
    private const LOOP_EACH_MS = 50;

    private SubscriberStreamer $subscriberStreamer;
    private ChannelsInterface $channels;
    private LoggerInterface $logger;

    /**
     * @param ChannelsInterface $channels
     * @param LoggerInterface $logger
     */
    public function __construct(
        ChannelsInterface $channels,
        LoggerInterface $logger
    ) {
        $this->channels = $channels;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function start(): void
    {
        try {
            $this->subscriberSetup();
            do {
                $startAt = microtime(true);
                // channel event poll & streamer iterate
                if (!$this->polling()) {
                    break;
                }
                // Route new message to service
                $this->routeMessage();

                // Wait a while - prevent CPU load
                $this->loopWait($startAt);
            } while (true);
        } catch (Throwable $reason) {
            // log this error & request respawning
            $this->logger->error(
                $reason->getMessage(),
                [
                    'component' => self::LOGGER_COMPONENT_PREFIX . "exception",
                    'error' => $reason
                ]
            );
            $this->channels->sendTo(
                $this->channels->getThreadChannel(),
                (new IPCMessage())->set(ParallelChannels::METHOD_RESPAWN_REQUESTED)
            );
        }
    }

    /**
     * @return bool
     */
    private function polling(): bool
    {
        // channel events pool
        $polling = $this->channels->eventsPoll();
        if ($this->channels->isAbortRequested()) {
            // send aborting message to main thread
            $this->channels->sendTo(
                $this->channels->getThreadChannel(),
                (new IPCMessage())->set(ParallelChannels::METHOD_ABORTING)
            );
            return false;
        }

        // subscriber iterate
        if (isset($this->subscriberStreamer)) {
            $this->subscriberStreamer->iterate();
        }

        return $polling;
    }

    /**
     * @return void
     */
    private function routeMessage(): void
    {
        if (!isset($this->subscriberStreamer)) {
            return;
        }
        if (!$this->subscriberStreamer->consumed()) {
            return;
        }

        try {
            /** @var Router $router */
            $router = app(RouterInterface::class);
            $router->route($this->subscriberStreamer->get());
        } catch (Throwable $reason) {
            $this->logger->error(
                $reason->getMessage(),
                [
                    'component' => self::LOGGER_COMPONENT_PREFIX . "route_message_exception",
                    'error' => $reason
                ]
            );

        }
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
                    $this->logger
                ))->brokerChannelsSetup();
                break;
            case "configuration":
                // wait after infrastructure to finish exchange/queues/bindings declarations
                usleep(rand(2000000, 4000000));
                // TODO: implement configuration listener - (centralized configuration server feature)
                break;
            case "worker":
                // wait after infrastructure to finish exchange/queues/bindings declarations
                usleep(rand(2000000, 5000000));
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
     * @param float $startAt
     *
     * @return void
     */
    private function loopWait(float $startAt): void
    {
        $loopWait = self::LOOP_EACH_MS - (round((microtime(true) - $startAt) * 1000));
        if ($loopWait > 0) {
            usleep(((int)$loopWait * 1000));
        }
    }
}
