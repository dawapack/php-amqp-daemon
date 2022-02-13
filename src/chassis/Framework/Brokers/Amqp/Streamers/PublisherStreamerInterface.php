<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\BrokerRequest;
use DaWaPack\Chassis\Framework\Brokers\Amqp\BrokerResponse;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Chassis\Framework\Brokers\Exceptions\StreamerChannelClosedException;

interface PublisherStreamerInterface
{
    /**
     * @return AckNackHandlerInterface
     */
    public function getAckHandler(): AckNackHandlerInterface;

    /**
     * @param AckNackHandlerInterface $ackHandler
     *
     * @return PublisherStreamerInterface
     */
    public function setAckHandler(AckNackHandlerInterface $ackHandler): PublisherStreamerInterface;

    /**
     * @return AckNackHandlerInterface
     */
    public function getNackHandler(): AckNackHandlerInterface;

    /**
     * @param AckNackHandlerInterface $nackHandler
     *
     * @return PublisherStreamerInterface
     */
    public function setNackHandler(AckNackHandlerInterface $nackHandler): PublisherStreamerInterface;

    /**
     * @param BrokerRequest|BrokerResponse $data
     * @param string|null $channelName
     * @param int|float $publishAcknowledgeTimeout
     *
     * @return void
     * @throws StreamerChannelClosedException
     */
    public function publish($data, ?string $channelName = null, $publishAcknowledgeTimeout = 5): void;
}
