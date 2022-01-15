<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationFactory;
use DaWaPack\Classes\Brokers\Amqp\Configurations\ConfigurationLoader;
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
        $this->sut = (new StreamConnectionFactory())(
            new ConfigurationFactory(),
            new ConfigurationLoader($this->app->get('config'))
        );
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
