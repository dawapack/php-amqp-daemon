<?php
declare(strict_types=1);

namespace DaWaPack\Models;

use DaWaPack\Classes\Brokers\Amqp\AbstractAMQPBroker;

class BrokerInboundEvents extends AbstractAMQPBroker
{
    protected string $channel = 'inbound/events';
    protected string $operation = self::SUBSCRIBE_OPERATION;
}
