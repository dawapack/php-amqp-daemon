<?php

namespace DaWaPack\Classes\Brokers\Configuration\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContractDefinitions extends DataTransferObject
{
    public string $contract;
    public ?string $infrastructure;
}