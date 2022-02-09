<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Contracts;

use Closure;
use DaWaPack\Classes\Brokers\Amqp\BrokerRequest;
use DaWaPack\Classes\Brokers\Amqp\BrokerResponse;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerChannel;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerChannelsCollection;
use DaWaPack\Classes\Brokers\Exceptions\StreamerChannelNameNotFoundException;

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
