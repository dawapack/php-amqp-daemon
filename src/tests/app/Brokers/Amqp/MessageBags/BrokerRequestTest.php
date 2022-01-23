<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\MessageBags;

use DaWaPack\Classes\Brokers\Amqp\BrokerRequest;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use DaWaPack\Classes\Brokers\Exceptions\MessageBagFormatException;
use DaWaPack\Tests\AppTestCase;
use JsonException;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use function DaWaPack\Chassis\Helpers\publish;
use function DaWaPack\Chassis\Helpers\subscribe;

class BrokerRequestTest extends AppTestCase
{
    protected bool $infrastructureDeclare = true;
    private BrokerRequest $sut;

    /**
     * @return void
     *
     * @throws JsonException
     * @throws MessageBagFormatException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new BrokerRequest(
            ["test" => __METHOD__],
            []
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
    public function testSutCanSendRequest(): void
    {
        $this->sut
            ->setRoutingKey("Any.RK.TestCommand")
            ->setReplyTo("DaWaPack.Q.TestCommands")
            ->setMessageType("doSomethingNiceRequest");
        publish($this->sut, "test/outbound/commands");
    }

    /**
     * @return void
     */
    public function testSutCanReceiveRequest(): void
    {
        $sut = subscribe("test/inbound/commands", BrokerRequest::class);
        $timeout = time() + 5;
        do {
            $sut->iterate();
            if ($sut->consumed()) {
                break;
            }
        } while ($timeout > time());

        $response = $sut->get();
        $this->assertInstanceOf(BrokerRequest::class, $response);
        $this->assertEquals("doSomethingNiceRequest", $response->getProperty("type"));
    }
}