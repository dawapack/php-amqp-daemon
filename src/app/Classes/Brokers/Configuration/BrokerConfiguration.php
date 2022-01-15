<?php

namespace DaWaPack\Classes\Brokers\Configuration;

use DaWaPack\Classes\Brokers\Configuration\DTO\BrokerContract;

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

}