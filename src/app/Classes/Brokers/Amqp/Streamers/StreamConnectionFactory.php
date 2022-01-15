<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class StreamConnectionFactory
{
    public function __invoke(
        BrokerConfigurationInterface $brokerConfiguration
    ): AMQPStreamConnection {
        return new AMQPStreamConnection(
            ...$brokerConfiguration->getConnectionConfiguration()->toFunctionArguments()
        );
    }
}
