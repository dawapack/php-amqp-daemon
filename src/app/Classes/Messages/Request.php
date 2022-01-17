<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

class Request extends AbstractRequestResponseMessage implements RequestInterface
{

    public function setRoutingKey(string $routingKey): Request
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    public function setMessageType(string $messageType): Request
    {
        $this->headers->type = $messageType;
        return $this;
    }

    public function setReplyTo(string $replyTo): Request
    {
        $this->headers->reply_to = $replyTo;
        return $this;
    }

    public function getMessageType(): string
    {
        return $this->getHeaders("type");
    }

    public function getReplyTo(): string
    {
        return $this->getHeaders("reply_to");
    }

    public function send(string $amqpChannelName): void
    {
//        app(BrokerConfigurationInterface::class);
    }
}
