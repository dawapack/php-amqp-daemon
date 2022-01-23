<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Messages;

use DateTime;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use DaWaPack\Classes\Messages\AbstractRequestResponseMessage;
use DaWaPack\Classes\Messages\Request;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

class RequestTest extends AppTestCase
{
    private Request $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Request(
            'this is a body string',
            [
                "content_encoding" => "UTF-8",
                "reply_to" => "OtherService.Q.Responses",
            ]
        );
    }

    public function testSutIsInstanceOfRequest()
    {
        $this->assertInstanceOf(Request::class, $this->sut);
    }

    public function testSutCanInitializeDefaultsHeaders()
    {
        $this->assertEquals("default", $this->sut->getMessageType());
    }

    public function testSutCanHandleMessageTypeHeader()
    {
        $this->assertEquals("default", $this->sut->getMessageType());
        $this->sut->setMessageType('doSomethingRequest');
        $this->assertEquals("doSomethingRequest", $this->sut->getMessageType());
    }

    public function testSutCanSetRoutingKey()
    {
        $this->sut->setRoutingKey('DaWaPack.RK.Responses');
        $routingKeyProperty = (new ReflectionClass(get_parent_class($this->sut)))->getProperty('routingKey');
        $routingKeyProperty->setAccessible(true);
        $this->assertEquals(
            "DaWaPack.RK.Responses",
            $routingKeyProperty->getValue($this->sut)
        );
    }

    public function testSutCanHandleReplyToHeader()
    {
        $this->assertEquals("OtherService.Q.Responses", $this->sut->getReplyTo());
        $this->sut->setReplyTo('DaWaPack.Q.Responses');
        $this->assertEquals("DaWaPack.Q.Responses", $this->sut->getReplyTo());
    }

    public function testSutCanSetAndGetBody()
    {
        $this->assertEquals("this is a body string", $this->sut->getBody());
        $this->sut->setBody("this is an other body string");
        $this->assertEquals("this is an other body string", $this->sut->getBody());
    }

    public function testSutCanReturnAmqpMessage()
    {
        $this->assertInstanceOf(AMQPMessage::class, $this->sut->toAmqpMessage());
    }

    public function testSutCanHandleTextPlainContentType()
    {
        $sut = new Request(
            'this is a body string',
            ["content_type" => AbstractRequestResponseMessage::TEXT_CONTENT_TYPE]
        );
        $this->assertEquals("this is a body string", ($sut->toAmqpMessage())->getBody());
    }

    public function testSutCanHandleJsonContentType()
    {
        $sut = new Request(
            ["test" => "ok"],
            ["content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE]
        );
        $this->assertEquals('{"test":"ok"}', ($sut->toAmqpMessage())->getBody());
    }

    public function testSutCanHandleGzipContentType()
    {
        $sut = new Request(
            json_encode(["test" => "ok"]),
            ["content_type" => AbstractRequestResponseMessage::GZIP_CONTENT_TYPE]
        );
        $this->assertEquals('eJyrVipJLS5RslLKz1aqBQAfLwRV', ($sut->toAmqpMessage())->getBody());
    }

    public function testSutCanHandleAnInboundJsonEncodedBody()
    {
        $message = [
            '{"test":"ok"}',
            [
                'content_type' => AbstractRequestResponseMessage::JSON_CONTENT_TYPE,
                'content_encoding' => AbstractRequestResponseMessage::DEFAULT_HEADER_CONTENT_ENCODING,
                'priority' => 0,
                'correlation_id' => (Uuid::uuid4())->toString(),
                'reply_to' => 'DaWaPack.Q.Responses',
                'message_id' => (Uuid::uuid4())->toString(),
                'type' => 'doSomethingRequest',
                'application_headers' => new AMQPTable([
                    'version' => '1.0.0',
                    'dateTime' => (new DateTime('now'))->format('Y-m-d H:i:s.v')
                ])
            ]
        ];
        $AMQPMessage = new AMQPMessage(...$message);
        $AMQPMessage->setConsumerTag("any#consume#tag");

        $sut = new Request(
            $AMQPMessage->getBody(),
            $AMQPMessage->get_properties(),
            $AMQPMessage->getConsumerTag()
        );

        $this->assertEquals("doSomethingRequest", $sut->getHeaders("type"));
        $this->assertIsObject($sut->getBody());
        $this->assertObjectHasAttribute("test", $sut->getBody());
        $this->assertEquals(
            AbstractRequestResponseMessage::JSON_CONTENT_TYPE,
            $sut->getHeaders("content_type")
        );
    }

//    public function testSutCanSendRequestMessage()
//    {
//        $sut = new Request(
//            ["test" => __METHOD__],
//            ["content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE]
//        );
//        $sut->setRoutingKey("Any.RK.TestCommand");
//
//        $app = $this->app;
//
//        $app->add(PublisherStreamerInterface::class, function ($sutClass, $app) {
//            $ackHandler = new class($sutClass) implements AckNackHandlerInterface {
//                private $sutClass;
//
//                public function __construct($sutClass)
//                {
//                    $this->sutClass = $sutClass;
//                }
//
//                public function handle(AMQPMessage $message): void
//                {
//                    $this->sutClass->assertInstanceOf(AMQPMessage::class, $message);
//                }
//            };
//            return (new PublisherStreamer(
//                $app->get('broker-streamer'),
//                $app->get(ContractsManagerInterface::class),
//                $app->get(LoggerInterface::class)
//            ))->setAckHandler($ackHandler);
//        })->addArguments([$this, $app]);
//
//        $sut->send("test/outbound/commands");
//    }
}
