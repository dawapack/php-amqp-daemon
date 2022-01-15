<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp;

use DaWaPack\Classes\Brokers\Amqp\Streamers\StreamerInterface;
use DaWaPack\Classes\Brokers\Exceptions\BrokerInvalidOperationException;

abstract class AbstractAMQPBroker implements BrokerInterface
{
    public const PUBLISH_OPERATION = 'publish';
    public const SUBSCRIBE_OPERATION = 'subscribe';
    protected StreamerInterface $streamer;
    protected string $channel;
    protected string $operation;
    private array $allowedOperations = [
        'publish' => true,
        'subscribe' => true,
        'rpc' => false,
    ];

    /**
     * @throws \DaWaPack\Classes\Brokers\Exceptions\BrokerInvalidOperationException
     */
    public function __construct()
    {
        if (!isset($this->operation) || !isset($this->allowedOperations[$this->operation])) {
            throw new BrokerInvalidOperationException(sprintf("Unknown operation '%s'", $this->operation));
        }
        if (!$this->allowedOperations[$this->operation]) {
            throw new BrokerInvalidOperationException(sprintf("Operation '%s' not allowed", $this->operation));
        }
    }

    final public function getChannel(): ?string
    {
        return $this->channel ?? null;
    }

    final public function getOperation(): ?string
    {
        return $this->operation ?? null;
    }

    final public function streamer(): ?StreamerInterface
    {
        return $this->streamer ?? null;
    }

    final public function setStreamer(StreamerInterface $streamer): BrokerInterface
    {
        $this->streamer = $streamer;
        return $this;
    }
}
