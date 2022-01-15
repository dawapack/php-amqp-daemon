<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationLoader;
use DaWaPack\Classes\Brokers\Amqp\Configurations\ConnectionConfiguration;
use DaWaPack\Tests\AppTestCase;

class ConnectionConfigurationTest extends AppTestCase
{
    private ConnectionConfiguration $sut;
    private array $connectionConfigurationKeys;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ConnectionConfiguration(
            new ConfigurationLoader($this->app->get("config"))
        );
        $this->connectionConfigurationKeys = require __DIR__ . "/Fixtures/broker.php";
    }

    public function testConnectionConfigurationIsLoaded()
    {
        $this->assertEquals(
            $this->connectionConfigurationKeys,
            array_keys($this->sut->toArray())
        );
    }

    public function testConnectionConfigurationReturnsConfigurationAsFunctionArguments()
    {
        $this->assertArrayNotHasKey( "protocol", $this->sut->toFunctionArguments(false));
    }
}
