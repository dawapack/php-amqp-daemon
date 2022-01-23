<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\MessageBags;

use DaWaPack\Classes\Brokers\Amqp\MessageBags\DTO\BagProperties;
use PhpAmqpLib\Message\AMQPMessage;

interface MessageBagInterface
{
    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getProperty(string $key);

    /**
     * @return BagProperties
     */
    public function getProperties(): BagProperties;

    /**
     * @var mixed $body
     *
     * @return $this
     */
    public function setBody($body): self;

    /**
     * @return mixed
     */
    public function getBody();

    /**
     * @param string $routingKey
     *
     * @return $this
     */
    public function setRoutingKey(string $routingKey): self;

    /**
     * @return string|null
     */
    public function getRoutingKey(): ?string;

    /**
     * @param string $messageType
     *
     * @return $this
     */
    public function setMessageType(string $messageType): self;

    /**
     * @param string $replyTo
     *
     * @return $this
     */
    public function setReplyTo(string $replyTo): self;

    /**
     * @return AMQPMessage
     */
    public function toAmqpMessage(): AMQPMessage;
}
