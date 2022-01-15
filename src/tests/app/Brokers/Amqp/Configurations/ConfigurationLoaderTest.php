<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationLoader;
use DaWaPack\Classes\Brokers\Exceptions\BrokerConfigurationException;
use DaWaPack\Tests\AppTestCase;

class ConfigurationLoaderTest extends AppTestCase
{
    private ConfigurationLoader $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ConfigurationLoader($this->app->get("config"));

    }

    public function testLoadingBrokerConfigurationMustFailForUnknownKey()
    {
        $this->expectException(BrokerConfigurationException::class);
        $this->sut->loadConfig("broker.mustRiseAnException");
    }

    public function testLoadingBrokerConfigurationMustFailForUnknownDetailsSection()
    {
        $this->expectException(BrokerConfigurationException::class);
        $this->sut->loadConfig("broker.connections.amqp.protocol");
    }

    public function testLoadingBrokerConnectionConfiguration()
    {
        $brokerConnection = $this->sut->loadConfig("broker.connection");
        $this->assertIsArray($brokerConnection);
        $this->assertArrayHasKey("protocol", $brokerConnection);
        $this->assertEquals("amqp", $brokerConnection["protocol"]);
    }

    public function testLoadingAmqpBrokerConnectionConfiguration()
    {
        $brokerConnection = $this->sut->loadConfig("broker.connections.amqp");
        $this->assertIsArray($brokerConnection);
        $this->assertArrayHasKey("protocol", $brokerConnection);
        $this->assertEquals("amqp", $brokerConnection["protocol"]);
    }

    public function testLoadingBrokerContractConfiguration()
    {
        $brokerContract = $this->sut->loadConfig("broker.contract");
        $this->assertIsArray($brokerContract);
        $this->assertArrayHasKey("driver", $brokerContract);
        $this->assertArrayHasKey("paths", $brokerContract);
        $this->assertArrayHasKey("definitions", $brokerContract);
    }

    public function testLoadingBrokerBidingsConfiguration()
    {
        $this->assertIsArray($this->sut->loadBindings('inbound/requests'));
    }
}
