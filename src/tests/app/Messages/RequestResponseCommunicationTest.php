<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Messages;

use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamerInterface;
use DaWaPack\Classes\Brokers\Exceptions\StreamerChannelNameNotFoundException;
use DaWaPack\Classes\Messages\AbstractRequestResponseMessage;
use DaWaPack\Classes\Messages\Request;
use DaWaPack\Classes\Messages\Response;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RequestResponseCommunicationTest extends AppTestCase
{
    protected bool $infrastructureDeclare = true;

    protected function setUp(): void
    {
        parent::setUp();
        $application = $this->app;

        // add PublisherStreamerInterface to container
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

    public function testCanSendRequest()
    {
        $sut = new Request(
            ["test" => __METHOD__],
            [
                "content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE,
                "reply_to" => "DaWaPack.Q.TestCommands"
            ]
        );
        $sut->setRoutingKey("Any.RK.TestCommand");
        $sut->send("test/outbound/commands");
    }

    public function testSendRequestThrowChannelNameNotFoundException()
    {
        $this->expectException(StreamerChannelNameNotFoundException::class);
        $sut = new Request(
            ["test" => __METHOD__],
            ["content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE]
        );
        $sut->setRoutingKey("Any.RK.TestCommand");
        $sut->send("test/outbound/test/error");
    }

    public function testCanSendResponse()
    {
        $sut = new Response(
            ["test" => __METHOD__],
            ["content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE]
        );
        $sut->setRoutingKey("Any.RK.TestResponseLoopback")
            ->setStatusCode(401)
            ->setStatusMessage("Unauthorized");
        $sut->send("test/outbound/responses");
    }

    public function testSendResponseThrowChannelNameNotFoundException()
    {
        $this->expectException(StreamerChannelNameNotFoundException::class);
        $sut = new Response(
            ["test" => __METHOD__],
            ["content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE]
        );
        $sut->setRoutingKey("Any.RK.TestResponseLoopback")
            ->setStatusCode(401)
            ->setStatusMessage("Unauthorized");
        $sut->send("test/outbound/test/error");
    }

    public function testCanReceiveRequest()
    {
        /** @var SubscriberStreamer $sut */
        $sut = $this->app->get(SubscriberStreamerInterface::class);
        $sut->setHandler(Request::class)
            ->setChannelName("test/inbound/commands")
            ->consume();
        $timeout = time() + 5;
        do {
            $sut->iterate();
            if ($sut->consumed()) {
                break;
            }
            // wait a while - prevent CPU load
            usleep(50000);
        } while ($timeout > time());

        $request = $sut->get();
        $this->assertInstanceOf(Request::class, $sut->get());
        if ($request instanceof Request) {
            $this->assertEquals("DaWaPack.Q.TestCommands", $request->getReplyTo());
        }

    }

    public function testCanReceiveResponse()
    {
        /** @var SubscriberStreamer $sut */
        $sut = $this->app->get(SubscriberStreamerInterface::class);
        $sut->setHandler(Response::class)
            ->setChannelName("test/inbound/responses")
            ->consume();
        $timeout = time() + 5;
        do {
            $sut->iterate();
            if ($sut->consumed()) {
                break;
            }
            // wait a while - prevent CPU load
            usleep(50000);
        } while ($timeout > time());

        $response = $sut->get();
        $this->assertInstanceOf(Response::class, $response);
        if ($response instanceof Response) {
            $this->assertEquals(401, $response->getStatusCode());
            $this->assertEquals("Unauthorized", $response->getStatusMessage());
        }
    }
}
