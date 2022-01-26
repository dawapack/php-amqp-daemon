<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerContractPath extends DataTransferObject
{
    public string $source;
    public string $validator;
}
