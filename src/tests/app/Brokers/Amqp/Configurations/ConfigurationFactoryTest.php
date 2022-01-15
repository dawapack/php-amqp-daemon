<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationFactory;
use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationLoader;
use DaWaPack\Classes\Brokers\Amqp\Configurations\ConnectionConfiguration;
use DaWaPack\Tests\AppTestCase;

class ConfigurationFactoryTest extends AppTestCase
{
    private ConfigurationFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ConfigurationFactory();
    }

    public function testConfigurationFactoryReturnsConnectionConfigurationConcrete()
    {

        $connectionConfigurationInstance = ($this->sut)(
            new ConfigurationLoader($this->app->get("config")),
            ConnectionConfiguration::class
        );
        $this->assertInstanceOf(ConnectionConfiguration::class, $connectionConfigurationInstance);
    }
}