<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Threads;

use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Threads\Configuration\ThreadConfiguration;
use DaWaPack\Classes\Threads\DTO\JobsProcessed;
use DaWaPack\Classes\Threads\Exceptions\ThreadInstanceException;
use DaWaPack\Classes\Workers\Worker;
use DaWaPack\Interfaces\ThreadInstanceInterface;
use DaWaPack\Interfaces\WorkerInterface;
use DaWaPack\Kernel;
use parallel\Channel;
use parallel\Channel\Error\Existence;
use parallel\Future;
use parallel\Runtime;
use Ramsey\Uuid\Uuid;
use Throwable;
use function DaWaPack\Chassis\Helpers\app;

class ThreadInstance implements ThreadInstanceInterface
{

    private const LOGGER_COMPONENT_PREFIX = "thread_instance_";

    private ThreadConfiguration $threadConfiguration;
    private JobsProcessed $jobsProcessed;
    private Future $future;
    private Channel $outgoingChannel;
    private Channel $incomingChannel;

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

        // Create thread outgoing channel
        if (($this->incomingChannel = $this->createIncomingChannel(
                $threadId . "-in",
                Channel::Infinite)
            ) === false
        ) {
            throw new ThreadInstanceException("creating incoming channel for thread instance fail");
        }

        // Create thread outgoing channel
        if (($this->outgoingChannel = $this->createOutgoingChannel(
                $threadId . "-out",
                Channel::Infinite)
            ) === false
        ) {
            $this->incomingChannel->close();
            throw new ThreadInstanceException("creating outgoing channel for thread instance fail");
        }

        // Create future
        if (($this->future = $this->createFuture($threadId)) === false) {
            $this->incomingChannel->close();
            $this->outgoingChannel->close();
            throw new ThreadInstanceException("creating future for thread instance fail");
        }

        return $threadId;
    }

    /**
     * @param string|null $reason
     *
     * @return bool
     */
    public function respawn(?string $reason = null): bool
    {
        return true;
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
     * @return Future|bool
     */
    private function createFuture(string $threadId)
    {
        // Create parallel runtime - inject vendor autoload as bootstrap
        $basePath = app('basePath');
        try {
            // Create parallel future
            return (new Runtime($basePath . "/vendor/autoload.php"))->run(
                static function (
                    string $threadId,
                    string $basePath,
                    string $brokerChannel,
                    Channel $incomingChannel,
                    Channel $outgoingChannel
                ): void {
                    // Define application in Closure as worker
                    define('RUNNER_TYPE', 'worker');
                    /** @var Application $app */
                    $app = require $basePath . '/bootstrap/app.php';
                    // Load configuration files
                    $app->withConfig("broker");
                    // Add aliases
                    $app->add('brokerChannel', $brokerChannel);
                    $app->add('incomingChannel', $incomingChannel);
                    $app->add('outgoingChannel', $outgoingChannel);
                    $app->add('threadId', $threadId);
                    // Add singleton
                    $app->add(WorkerInterface::class, Worker::class);
                    // Start processing jobs
                    (new Kernel($app))->boot();
                }, [
                    $threadId,
                    $basePath,
                    $this->threadConfiguration->channelName,
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

        return false;
    }
}
