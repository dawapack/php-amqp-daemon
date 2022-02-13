<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts;

use Closure;
use DaWaPack\Chassis\Framework\Brokers\Amqp\BrokerRequest;
use DaWaPack\Chassis\Framework\Brokers\Amqp\BrokerResponse;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannel;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannelsCollection;
use DaWaPack\Chassis\Framework\Brokers\Exceptions\StreamerChannelNameNotFoundException;

interface ContractsManagerInterface
{
    /**
     * @param string $channelName
     *
     * @return BrokerChannel|null
     */
    public function getChannel(string $channelName): ?BrokerChannel;

    /**
     * @return BrokerChannelsCollection
     */
    public function getChannels(): BrokerChannelsCollection;

    /**
     * @param string $channelName
     * @param BrokerRequest|BrokerResponse $data
     *
     * @return array
     *
     * @throws StreamerChannelNameNotFoundException
     */
    public function toBasicPublishFunctionArguments(string $channelName, $data): array;

    /**
     * @param string $channelName
     * @param Closure $callback
     *
     * @return array
     *
     * @throws StreamerChannelNameNotFoundException
     */
    public function toBasicConsumeFunctionArguments(string $channelName, Closure $callback): array;

    /**
     * @return array
     */
    public function toStreamConnectionFunctionArguments(): array;

    /**
     * @return array
     */
    public function toLazyConnectionFunctionArguments(): array;
}
