<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Streamers\AbstractStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\StreamConnectionFactory;
use DaWaPack\Classes\Brokers\Amqp\Streamers\StreamerInterface;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class AbstractStreamerTest extends AppTestCase
{

    /**
     * @var StreamerInterface
     */
    private StreamerInterface $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfigurationFixture = require __DIR__ . "/../Fixtures/Config/broker.php";
        $amqpStreamConnection = (new StreamConnectionFactory())(
            new BrokerConfiguration($brokerConfigurationFixture)
        );
        $this->sut = new class(
            $amqpStreamConnection, 'outbound/requests', 'publish'
        ) extends AbstractStreamer {};
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
