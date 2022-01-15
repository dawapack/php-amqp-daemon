<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContract extends DataTransferObject
{
    public string $driver;
    public BrokerContractPath $paths;
    public BrokerContractDefinitions $definitions;
}
