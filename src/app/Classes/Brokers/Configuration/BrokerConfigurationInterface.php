<?php

namespace DaWaPack\Classes\Brokers\Configuration;

use DaWaPack\Classes\Brokers\Configuration\DTO\BrokerContract;

interface BrokerConfigurationInterface
{
    public function getContract(): string;

    public function getContractConfiguration(): BrokerContract;
}