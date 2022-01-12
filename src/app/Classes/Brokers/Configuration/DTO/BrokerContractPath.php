<?php

namespace DaWaPack\Classes\Brokers\Configuration\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContractPath extends DataTransferObject
{
    public string $source;
    public string $validator;
}