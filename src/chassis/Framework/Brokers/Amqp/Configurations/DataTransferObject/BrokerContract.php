<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContract extends DataTransferObject
{
    /**
     * @var string
     */
    public string $driver;

    /**
     * @var \DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerContractPath
     */
    public BrokerContractPath $paths;

    /**
     * @var \DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerContractDefinitions
     */
    public BrokerContractDefinitions $definitions;
}
