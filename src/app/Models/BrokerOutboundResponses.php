<?php
declare(strict_types=1);

namespace DaWaPack\Models;

use DaWaPack\Classes\Brokers\Amqp\AbstractAMQPBroker;

class BrokerOutboundResponses extends AbstractAMQPBroker
{
    protected string $channel = 'outbound/responses';
    protected string $operation = self::SUBSCRIBE_OPERATION;
}
