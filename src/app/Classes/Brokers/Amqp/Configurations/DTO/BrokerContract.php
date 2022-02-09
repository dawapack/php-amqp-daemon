<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContract extends DataTransferObject
{
    /**
     * @var string
     */
    public string $driver;

    /**
     * @var \DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerContractPath
     */
    public BrokerContractPath $paths;

    /**
     * @var \DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerContractDefinitions
     */
    public BrokerContractDefinitions $definitions;
}
