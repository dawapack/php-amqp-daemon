<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContractPath extends DataTransferObject
{
    /**
     * @var string
     */
    public string $source;

    /**
     * @var string
     */
    public string $validator;
}
