<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Streamers\InfrastructureStreamer;
use DaWaPack\Tests\AppTestCase;
use Psr\Log\LoggerInterface;
use function DaWaPack\Chassis\Helpers\app;

class InfrastructureStreamerTest extends AppTestCase
{
    private InfrastructureStreamer $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfiguration = app(BrokerConfigurationInterface::class);
        $this->sut = new InfrastructureStreamer(
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