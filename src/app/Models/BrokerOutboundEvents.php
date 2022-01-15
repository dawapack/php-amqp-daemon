<?php
declare(strict_types=1);

namespace DaWaPack\Models;

use DaWaPack\Classes\Brokers\Amqp\AbstractAMQPBroker;

class BrokerOutboundEvents extends AbstractAMQPBroker
{
    protected string $channel = 'outbound/events';
    protected string $operation = self::SUBSCRIBE_OPERATION;
}
