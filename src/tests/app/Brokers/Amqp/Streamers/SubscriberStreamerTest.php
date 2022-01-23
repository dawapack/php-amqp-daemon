<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Classes\Messages\Response;
use DaWaPack\Tests\AppTestCase;
use Psr\Log\LoggerInterface;

class SubscriberStreamerTest extends AppTestCase
{
    private SubscriberStreamer $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new SubscriberStreamer(
            $this->app->get('broker-streamer'),
            $this->app->get(ContractsManagerInterface::class),
            $this->app->get(LoggerInterface::class)
        );
    }

    public function testSutIsInstanceOfSubscriberStreamer()
    {
        $this->assertInstanceOf(SubscriberStreamer::class, $this->sut);
    }

    public function testSutCanSetHandler()
    {
        $this->assertNull($this->sut->getHandler());
        $this->sut->setHandler(Response::class);
        $this->assertEquals(Response::class, $this->sut->getHandler());
    }

    public function testSutCanSetQosPrefetchSize()
    {
        $this->assertNull($this->sut->getQosPrefetchSize());
        $this->sut->setQosPrefetchSize(200);
        $this->assertEquals(200, $this->sut->getQosPrefetchSize());
    }

    public function testSutCanSetQosPrefetchCount()
    {
        $this->assertNull($this->sut->getQosPrefetchCount());
        $this->sut->setQosPrefetchCount(10);
        $this->assertEquals(10, $this->sut->getQosPrefetchCount());
    }

    public function testSutCanSetQosPerConsumer()
    {
        $this->sut->setQosPerConsumer(true);
        $this->assertEquals(true, $this->sut->isQosPerConsumer());
        $this->sut->setQosPerConsumer(false);
        $this->assertEquals(false, $this->sut->isQosPerConsumer());
    }

    public function testSutCanReturnUninitializedData()
    {
        $this->assertNull($this->sut->get());
    }
}
