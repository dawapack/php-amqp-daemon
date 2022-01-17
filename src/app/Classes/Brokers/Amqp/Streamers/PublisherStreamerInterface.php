<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Exceptions\StreamerChannelClosedException;
use DaWaPack\Classes\Messages\Request;

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
     * @param Request $request
     * @param int|float $publishAcknowledgeTimeout
     *
     * @return void
     * @throws StreamerChannelClosedException
     */
    public function publish(Request $request, $publishAcknowledgeTimeout = 5): void;
}
