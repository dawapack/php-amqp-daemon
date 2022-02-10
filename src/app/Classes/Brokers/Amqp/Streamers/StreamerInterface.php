<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;

interface StreamerInterface
{
    /**
     * @param int|null $id
     *
     * @return AMQPChannel
     */
    public function getChannel(?int $id = null): AMQPChannel;

    /**
     * @return string|null
     */
    public function getChannelName(): ?string;

    /**
     * @param string $channelName
     *
     * @return $this
     */
    public function setChannelName(string $channelName): self;

    /**
     * @return string|null
     */
    public function getQueueName(): ?string;

    /**
     * @return bool
     */
    public function disconnect(): bool;
}
