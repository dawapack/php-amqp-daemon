<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\InfrastructureStreamer;
use DaWaPack\Tests\AppTestCase;
use Psr\Log\LoggerInterface;
use function DaWaPack\Chassis\Helpers\app;

class InfrastructureStreamerTest extends AppTestCase
{
    private \DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\InfrastructureStreamer $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfiguration = app(BrokerConfigurationInterface::class);
        $this->sut = new \DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\InfrastructureStreamer(
            app()->get('broker-streamer'),
            new ContractsManager($brokerConfiguration, new ContractsValidator()),
            app(LoggerInterface::class)
        );

        $this->markTestSkipped('skip these tests');
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
