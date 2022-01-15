<?php
declare(strict_types=1);

namespace DaWaPack\Models;

use DaWaPack\Classes\Brokers\Amqp\AbstractAMQPBroker;

class BrokerInboundResponses extends AbstractAMQPBroker
{
    protected string $channel = 'inbound/responses';
    protected string $operation = self::SUBSCRIBE_OPERATION;
}
