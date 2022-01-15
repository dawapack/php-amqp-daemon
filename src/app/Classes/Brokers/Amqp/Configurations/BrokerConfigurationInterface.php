<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerConnection;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerContract;

interface BrokerConfigurationInterface
{
    public function getContract(): string;
    public function getContractConfiguration(): BrokerContract;
    public function getConnection(): string;
    public function getConnectionConfiguration(): BrokerConnection;
}
