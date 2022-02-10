<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class BrokerChannelsCollection extends DataTransferObjectCollection
{
    /**
     * @return BrokerChannel
     */
    public function current(): BrokerChannel
    {
        return parent::current();
    }
}
