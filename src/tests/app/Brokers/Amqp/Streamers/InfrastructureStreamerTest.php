<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Streamers\InfrastructureStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\StreamConnectionFactory;
use DaWaPack\Tests\AppTestCase;

class InfrastructureStreamerTest extends AppTestCase
{
    private InfrastructureStreamer $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfigurationFixture = require __DIR__ . "/../Fixtures/Config/broker.php";
        $amqpStreamConnection = (new StreamConnectionFactory())(
            new BrokerConfiguration($brokerConfigurationFixture)
        );
        $this->sut = new InfrastructureStreamer(
            $amqpStreamConnection,
            new ContractsManager(
                new BrokerConfiguration($brokerConfigurationFixture),
                new ContractsValidator()
            )
        );
    }

    public function testSutIsInstanceOfInfrastructureStreamer()
    {
        $this->assertInstanceOf(InfrastructureStreamer::class, $this->sut);
    }

    public function testSutCanSetupAndDeleteChannels()
    {
        $this->sut->brokerChannelsSetup();
        $this->assertTrue(count($this->sut->getAvailableChannels("exchanges")) > 0);
        $this->assertTrue(count($this->sut->getAvailableChannels("queues")) > 0);

        $this->sut->brokerChannelsClear();
        $this->assertTrue(count($this->sut->getAvailableChannels("exchanges")) == 0);
        $this->assertTrue(count($this->sut->getAvailableChannels("queues")) == 0);
    }
}