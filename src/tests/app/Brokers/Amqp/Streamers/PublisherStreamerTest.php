<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Streamers;

use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamer;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class PublisherStreamerTest extends AppTestCase
{
    protected bool $infrastructureDeclare = true;
    private PublisherStreamer $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new PublisherStreamer(
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
        $this->assertInstanceOf(PublisherStreamer::class, $this->sut);
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
