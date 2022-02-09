<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerConnection;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerContract;

interface BrokerConfigurationInterface
{
    /**
     * @return string
     */
    public function getContract(): string;

    /**
     * @return BrokerContract
     */
    public function getContractConfiguration(): BrokerContract;

    /**
     * @return string
     */
    public function getConnection(): string;

    /**
     * @return BrokerConnection
     */
    public function getConnectionConfiguration(): BrokerConnection;
}
