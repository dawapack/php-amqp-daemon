<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Streamers\StreamConnectionFactory;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class StreamConnectionFactoryTest extends AppTestCase
{
    private AMQPStreamConnection $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfigurationFixture = require __DIR__ . "/../Fixtures/Config/broker.php";
        $this->sut = (new StreamConnectionFactory())(new BrokerConfiguration($brokerConfigurationFixture));
    }

    protected function tearDown(): void
    {
        if ($this->sut->isConnected()) {
            $this->sut->close();
        }
    }

    public function testSutIsAmqpStreamConnectionInstance()
    {
        $this->assertInstanceOf(AMQPStreamConnection::class, $this->sut);
    }

    public function testSutIsConnected()
    {
        $this->assertTrue($this->sut->isConnected());
    }

    public function testSutCanDeliverAChannel()
    {
        $this->assertInstanceOf(AMQPChannel::class, $this->sut->channel());
    }
}
