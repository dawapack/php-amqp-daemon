<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Exceptions\BrokerConfigurationException;

interface ConfigurationLoaderInterface
{
    /**
     * @param string $key
     *
     * @return array
     *
     * @throws BrokerConfigurationException
     */
    public function loadConfig(string $key): array;

    /**
     * @param string $channel
     *
     * @return array
     */
    public function loadBindings(string $channel): array;
}
