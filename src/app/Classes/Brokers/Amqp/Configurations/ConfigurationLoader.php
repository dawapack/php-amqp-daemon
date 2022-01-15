<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Chassis\Classes\Config\Configuration;
use DaWaPack\Classes\Brokers\Exceptions\BrokerConfigurationException;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    private Configuration $applicationConfig;

    /**
     * @inheritDoc
     */
    public function __construct(Configuration $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
    }


    /**
     * @inheritDoc
     */
    public function loadConfig(string $key): array
    {
        $configuration = $this->applicationConfig->get($key);
        if (is_array($configuration)) {
            return $configuration;
        } elseif (is_string($configuration)) {
            $detailsKey = $key . 's.' . $configuration;
        } else {
            throw new BrokerConfigurationException("Unable to load config properties from key '$key'");
        }
        return $this->loadFromDetailsSection($detailsKey);
    }

    /**
     * @inheritDoc
     */
    public function loadBindings(string $channel): array
    {
        return [];
    }

    /**
     * @param string $detailsKey
     *
     * @return array
     *
     * @throws BrokerConfigurationException
     */
    protected function loadFromDetailsSection(string $detailsKey): array
    {
        $configuration = $this->applicationConfig->get($detailsKey);
        if (is_null($configuration)) {
            throw new BrokerConfigurationException("Unable to load config properties from key '$detailsKey'");
        }
        return $configuration;
    }
}
