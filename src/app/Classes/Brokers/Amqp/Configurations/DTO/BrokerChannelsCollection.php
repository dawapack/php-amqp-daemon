<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class BrokerChannelsCollection extends DataTransferObjectCollection
{
    public function current(): BrokerChannel
    {
        return parent::current();
    }
}
