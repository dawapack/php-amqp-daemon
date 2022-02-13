<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\MessageBags;

use DaWaPack\Chassis\Framework\Brokers\Amqp\BrokerResponse;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\PublisherStreamer;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use DaWaPack\Chassis\Framework\Brokers\Exceptions\MessageBagFormatException;
use DaWaPack\Tests\AppTestCase;
use JsonException;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use function DaWaPack\Chassis\Helpers\publish;
use function DaWaPack\Chassis\Helpers\subscribe;

class BrokerResponseTest extends AppTestCase
{
    protected bool $infrastructureDeclare = true;
    private BrokerResponse $sut;

    /**
     * @return void
     *
     * @throws JsonException
     * @throws MessageBagFormatException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new BrokerResponse(
            ["test" => __METHOD__],
            [

            ]
        );

        // add custom PublisherStreamerInterface to container
        $application = $this->app;
        if (!$application->has(PublisherStreamerInterface::class)) {
            $application->add(PublisherStreamerInterface::class, function ($sutClass, $app) {
                $ackHandler = new class($sutClass) implements AckNackHandlerInterface {
                    private $sutClass;

                    public function __construct($sutClass)
                    {
                        $this->sutClass = $sutClass;
                    }

                    public function handle(AMQPMessage $message): void
                    {
                        $this->sutClass->assertInstanceOf(AMQPMessage::class, $message);
                    }
                };
                return (new PublisherStreamer(
                    $app->get('broker-streamer'),
                    $app->get(ContractsManagerInterface::class),
                    $app->get(LoggerInterface::class)
                ))->setAckHandler($ackHandler);
            })->addArguments([$this, $application]);
        }
    }

    /**
     * @return void
     */
    public function testSutCanSendResponse(): void
    {
        $this->sut
            ->setRoutingKey("Any.RK.TestResponseLoopback")
            ->setReplyTo("DaWaPack.Q.TestResponses")
            ->setMessageType("doSomethingNiceResponse");
        publish($this->sut, "test/outbound/responses");
    }

    /**
     * @return void
     */
    public function testSutCanReceiveResponse(): void
    {
        $sut = subscribe("test/inbound/responses", BrokerResponse::class);
        $timeout = time() + 5;
        do {
            $sut->iterate();
            if ($sut->consumed()) {
                break;
            }
        } while ($timeout > time());

        $response = $sut->get();
        $this->assertInstanceOf(BrokerResponse::class, $response);
        $this->assertEquals("doSomethingNiceResponse", $response->getProperty("type"));
    }
}
