<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Threads;

use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamerInterface;
use DaWaPack\Classes\Threads\Configuration\ThreadConfiguration;
use DaWaPack\Classes\Threads\DTO\JobsProcessed;
use DaWaPack\Classes\Threads\Exceptions\ThreadInstanceException;
use DaWaPack\Classes\Workers\Worker;
use DaWaPack\Classes\Workers\WorkerInterface;
use DaWaPack\Kernel;
use parallel\Channel;
use parallel\Channel\Error\Existence;
use parallel\Future;
use parallel\Runtime;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

use function DaWaPack\Chassis\Helpers\app;

class ThreadInstance implements ThreadInstanceInterface
{
    private const LOGGER_COMPONENT_PREFIX = "thread_instance_";

    private ThreadConfiguration $threadConfiguration;
    private JobsProcessed $jobsProcessed;
    private ?Future $future;
    private ?Channel $outgoingChannel;
    private ?Channel $incomingChannel;

    public function __destruct()
    {
        if (isset($this->incomingChannel)) {
            $this->incomingChannel->close();
        }
        if (isset($this->outgoingChannel)) {
            $this->outgoingChannel->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function setConfiguration(ThreadConfiguration $threadConfiguration): void
    {
        $this->threadConfiguration = $threadConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function getConfiguration(?string $key = null)
    {
        return !is_null($key)
            ? $this->threadConfiguration->{$key} ?? null
            : $this->threadConfiguration->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getFuture(): Future
    {
        return $this->future;
    }

    /**
     * @inheritDoc
     */
    public function getOutgoingChannel(): Channel
    {
        return $this->outgoingChannel;
    }

    /**
     * @inheritDoc
     */
    public function getIncomingChannel(): Channel
    {
        return $this->incomingChannel;
    }

    /**
     * @return string
     */
    public function spawn(): string
    {
        $threadId = (Uuid::uuid4())->toString();

        // Create thread incoming & outgoing channels
        $this->incomingChannel = $this->createIncomingChannel($threadId . "-in", Channel::Infinite);
        $this->outgoingChannel = $this->createOutgoingChannel($threadId . "-out", Channel::Infinite);
        if (is_null($this->incomingChannel) || is_null($this->outgoingChannel)) {
            throw new ThreadInstanceException("creating channels for thread instance fail");
        }

        // Create future
        $this->future = $this->createFuture($threadId);
        if (is_null($this->future)) {
            $this->incomingChannel->close();
            $this->outgoingChannel->close();
            throw new ThreadInstanceException("creating future for thread instance fail");
        }

        return $threadId;
    }

    /**
     * @param string $name
     * @param int $capacity
     *
     * @return Channel|null
     */
    private function createIncomingChannel(string $name, int $capacity): ?Channel
    {
        try {
            return (Channel::make($name, $capacity))::open($name);
        } catch (Existence $reason) {
            app()->logger()->error(
                $reason->getMessage(),
                [
                    "component" => self::LOGGER_COMPONENT_PREFIX . "create_incoming_channel_exception",
                    "error" => $reason
                ]
            );
        }

        return null;
    }

    /**
     * @param string $name
     * @param int $capacity
     *
     * @return Channel|null
     */
    private function createOutgoingChannel(string $name, int $capacity): ?Channel
    {
        try {
            return (Channel::make($name, $capacity))::open($name);
        } catch (Existence $reason) {
            app()->logger()->error(
                $reason->getMessage(),
                [
                    "component" => self::LOGGER_COMPONENT_PREFIX . "create_outgoing_channel_exception",
                    "error" => $reason
                ]
            );
        }

        return null;
    }

    /**
     * @param string $threadId
     *
     * @return Future|null
     */
    private function createFuture(string $threadId): ?Future
    {
        // Create parallel runtime - inject vendor autoload as bootstrap
        try {
            $basePath = app('basePath');
            // Create parallel future
            return (new Runtime($basePath . "/vendor/autoload.php"))->run(
                static function (
                    string $threadId,
                    string $basePath,
                    array $threadConfiguration,
                    Channel $incomingChannel,
                    Channel $outgoingChannel
                ): void {
                    // Define application in Closure as worker
                    define('RUNNER_TYPE', 'worker');

                    /** @var Application $app */
                    $app = require $basePath . '/bootstrap/app.php';

                    // Load configuration files
                    $app->withConfig("broker");
                    $app->withConfig("threads");

                    // Add aliases
                    $app->add('incomingChannel', $incomingChannel);
                    $app->add('outgoingChannel', $outgoingChannel);
                    $app->add('threadId', $threadId);
                    $app->add('threadConfiguration', $threadConfiguration);

                    // Add singletons
                    $app->add(WorkerInterface::class, Worker::class);
                    $app->add(BrokerConfigurationInterface::class, BrokerConfiguration::class)
                        ->addArgument($app->config("broker"));
                    $app->add(ContractsManagerInterface::class, function ($app) {
                        return new ContractsManager(
                            $app->get(BrokerConfigurationInterface::class),
                            new ContractsValidator()
                        );
                    })->addArgument($app);
                    $app->add('brokerStreamConnection', function ($app) {
                        $streamConnectionArguments = array_values(
                            $app->get(ContractsManagerInterface::class)->toStreamConnectionFunctionArguments()
                        );
                        return new AMQPStreamConnection(...$streamConnectionArguments);
                    })->addArgument($app)->setShared(false);
                    $app->add(SubscriberStreamerInterface::class, function ($app) {
                        return new SubscriberStreamer(
                            $app->get('brokerStreamConnection'),
                            $app->get(ContractsManagerInterface::class),
                            $app->get(LoggerInterface::class)
                        );
                    })->addArgument($app)->setShared(false);

                    // Start processing jobs
                    (new Kernel($app))->boot();
                },
                [
                    $threadId,
                    $basePath,
                    $this->threadConfiguration->toArray(),
                    $this->incomingChannel,
                    $this->outgoingChannel
                ]
            );
        } catch (Throwable $reason) {
            app()->logger()->error(
                $reason->getMessage(),
                [
                    "component" => self::LOGGER_COMPONENT_PREFIX . "create_future_exception",
                    "error" => $reason
                ]
            );
        }

        return null;
    }
}
