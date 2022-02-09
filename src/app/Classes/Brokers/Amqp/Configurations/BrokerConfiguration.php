<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerConnection;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerContract;

class BrokerConfiguration implements BrokerConfigurationInterface
{
    private array $configuration;

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function getContract(): string
    {
        return $this->configuration['contract'];
    }

    /**
     * @inheritdoc
     */
    public function getContractConfiguration(): BrokerContract
    {
        return new BrokerContract($this->configuration['contracts'][$this->getContract()]);
    }

    /**
     * @inheritdoc
     */
    public function getConnection(): string
    {
        return $this->configuration['connection'];
    }

    /**
     * @inheritdoc
     */
    public function getConnectionConfiguration(): BrokerConnection
    {
        return new BrokerConnection($this->configuration['connections'][$this->getConnection()]);
    }
}
