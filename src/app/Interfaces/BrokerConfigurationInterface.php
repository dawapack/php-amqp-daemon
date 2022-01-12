<?php

namespace DaWaPack\Interfaces;

use DaWaPack\Classes\Brokers\Configuration\DTO\BrokerContract;

interface BrokerConfigurationInterface
{
    public function getContract(): string;

    public function getContractConfiguration(): BrokerContract;
}