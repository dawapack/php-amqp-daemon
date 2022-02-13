<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerConnection;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerContract;

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
