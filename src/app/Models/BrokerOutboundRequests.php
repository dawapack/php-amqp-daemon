<?php
declare(strict_types=1);

namespace DaWaPack\Models;

use DaWaPack\Classes\Brokers\Amqp\AbstractAMQPBroker;

class BrokerOutboundRequests extends AbstractAMQPBroker
{
    protected string $channel = 'outbound/requests';
    protected string $operation = self::PUBLISH_OPERATION;
}
