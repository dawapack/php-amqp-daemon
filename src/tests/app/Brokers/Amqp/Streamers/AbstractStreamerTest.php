<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\AbstractStreamer;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\StreamerInterface;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use Psr\Log\LoggerInterface;
use function DaWaPack\Chassis\Helpers\app;

class AbstractStreamerTest extends AppTestCase
{

    /**
     * @var \DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\StreamerInterface
     */
    private StreamerInterface $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfiguration = app(BrokerConfigurationInterface::class);
        $this->sut = new class(
            app()->get('broker-streamer'),
            new ContractsManager($brokerConfiguration, new ContractsValidator()),
            app(LoggerInterface::class)
        ) extends AbstractStreamer {
        };
    }

    public function testSutIsAnInstanceImplementingAStreamerInterface()
    {
        $this->assertInstanceOf(StreamerInterface::class, $this->sut);
    }

    public function testSutCanDeliverAChannel()
    {
        $this->assertInstanceOf(AMQPChannel::class, $this->sut->getChannel());
    }

    public function testSutCanBeDisconnected()
    {
        $this->assertTrue($this->sut->disconnect());
    }

    public function testSutCanBeDisconnectedOnDestruct()
    {
        $this->expectException(AMQPConnectionClosedException::class);
        $this->sut->__destruct();
        $this->sut->getChannel();
    }

    public function testSutIsFaultTolerantOnDoubleDisconnect()
    {
        $this->assertTrue($this->sut->disconnect());
        $this->assertFalse($this->sut->disconnect());
    }
}
