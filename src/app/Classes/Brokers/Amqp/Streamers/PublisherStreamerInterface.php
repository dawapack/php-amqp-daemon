<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\BrokerRequest;
use DaWaPack\Classes\Brokers\Amqp\BrokerResponse;
use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Exceptions\StreamerChannelClosedException;

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
