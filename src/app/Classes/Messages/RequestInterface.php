<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

interface RequestInterface
{

    /**
     * Set the message type as discriminator to your outbound request
     *
     * @param string $messageType
     *
     * @return Request
     */
    public function setMessageType(string $messageType): Request;

    /**
     * Get the discriminator from an inbound request headers
     *
     * @return string
     */
    public function getMessageType(): string;

    /**
     * Routing key used to deliver this request by the broker routing mechanism
     *
     * @param string $routingKey
     *
     * @return $this
     */
    public function setRoutingKey(string $routingKey): Request;

    /**
     * Routing key will be used by the receiver to respond to your request
     *
     * @param string $replyTo
     *
     * @return $this
     */
    public function setReplyTo(string $replyTo): Request;

    /**
     * Get reply to property from an inbound request headers
     * @return string
     */
    public function getReplyTo(): string;

    /**
     * Send a request through an AMQP channel
     *
     * @param string $amqpChannelName
     */
    public function send(string $amqpChannelName): void;
}
