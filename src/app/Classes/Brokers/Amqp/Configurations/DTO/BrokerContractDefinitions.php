<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContractDefinitions extends DataTransferObject
{
    public string $contract;
    public ?string $infrastructure;
}
