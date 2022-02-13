<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Logger;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannel;
use DaWaPack\Chassis\Framework\Logger\DataTransferObject\ContextBroker;
use PhpAmqpLib\Message\AMQPMessage;

interface LoggerApplicationContextInterface
{
    /**
     * @return ContextBroker|null
     */
    public function getBrokerContext(): ?ContextBroker;

    /**
     * @param string $channelName
     * @param BrokerChannel $channel
     * @param AMQPMessage $message
     *
     * @return void
     */
    public function setBrokerContext(string $channelName, BrokerChannel $channel, AMQPMessage $message): void;

    /**
     * @return void
     */
    public function clear(): void;
}
