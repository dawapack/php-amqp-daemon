<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

class ConfigurationFactory implements ConfigurationFactoryInterface
{

    /**
     * @param ConfigurationLoaderInterface $configurationLoader
     * @param string $configurationClass
     *
     * @return ConfigurationInterface
     */
    public function __invoke(
        ConfigurationLoaderInterface $configurationLoader,
        string $configurationClass
    ): ConfigurationInterface {
        return new $configurationClass($configurationLoader);
    }
}
