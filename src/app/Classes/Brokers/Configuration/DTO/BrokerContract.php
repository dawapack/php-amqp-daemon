<?php

namespace DaWaPack\Classes\Brokers\Configuration\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContract extends DataTransferObject
{
    public string $driver;
    public BrokerContractPath $path;
    public BrokerContractDefinitions $definitions;
}