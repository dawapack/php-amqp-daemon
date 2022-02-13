<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class PublisherStreamerTest extends AppTestCase
{
    protected bool $infrastructureDeclare = true;
    private \DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\PublisherStreamer $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new \DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\PublisherStreamer(
            $this->app->get('broker-streamer'),
            $this->app->get(ContractsManagerInterface::class),
            $this->app->get(LoggerInterface::class)
        );

        $this->sut->setAckHandler(new class() implements AckNackHandlerInterface {
            public function handle(AMQPMessage $message): void
            {
            }
        });

        $this->sut->setNackHandler(new class() implements AckNackHandlerInterface {
            public function handle(AMQPMessage $message): void
            {
            }
        });
    }

    public function testSutIsInstanceOfPublisherStreamer()
    {
        $this->assertInstanceOf(\DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\PublisherStreamer::class, $this->sut);
    }

    public function testSutCanSetAcknowledgementHandler()
    {
        $this->assertInstanceOf(AckNackHandlerInterface::class, $this->sut->getAckHandler());
    }

    public function testSutCanSetNegativeAcknowledgementHandler()
    {
        $this->assertInstanceOf(AckNackHandlerInterface::class, $this->sut->getNackHandler());
    }
}
