<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationFactoryInterface;
use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationLoaderInterface;
use DaWaPack\Classes\Brokers\Amqp\Configurations\ConnectionConfiguration;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class StreamConnectionFactory
{

    /**
     *
     * @param ConfigurationFactoryInterface $configurationFactory
     * @param ConfigurationLoaderInterface $configurationLoader
     *
     * @return AMQPStreamConnection
     *
     */
    public function __invoke(
        ConfigurationFactoryInterface $configurationFactory,
        ConfigurationLoaderInterface $configurationLoader
    ): AMQPStreamConnection {
        $configuration = ($configurationFactory)($configurationLoader, ConnectionConfiguration::class)
            ->toFunctionArguments();
        return new AMQPStreamConnection(...$configuration);
    }
}
