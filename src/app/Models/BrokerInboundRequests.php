<?php
declare(strict_types=1);

namespace DaWaPack\Models;

use DaWaPack\Classes\Brokers\Amqp\AbstractAMQPBroker;

class BrokerInboundRequests extends AbstractAMQPBroker
{
    protected string $channel = 'inbound/requests';
    protected string $operation = self::SUBSCRIBE_OPERATION;
}
