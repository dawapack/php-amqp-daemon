<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerConnection;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerContract;

class BrokerConfiguration implements BrokerConfigurationInterface
{
    private array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getContract(): string
    {
        return $this->configuration['contract'];
    }

    public function getContractConfiguration(): BrokerContract
    {
        return new BrokerContract($this->configuration['contracts'][$this->getContract()]);
    }

    public function getConnection(): string
    {
        return $this->configuration['connection'];
    }

    public function getConnectionConfiguration(): BrokerConnection
    {
        return new BrokerConnection($this->configuration['connections'][$this->getConnection()]);
    }
}
