<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Messages;

use DateTime;
use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use DaWaPack\Classes\Messages\AbstractRequestResponseMessage;
use DaWaPack\Classes\Messages\Response;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

class ResponseTest extends AppTestCase
{
    private Response $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Response(
            'this is a body string',
            ["content_encoding" => "UTF-8"]
        );
    }

    public function testSutIsInstanceOfRequest()
    {
        $this->assertInstanceOf(Response::class, $this->sut);
    }

    public function testSutCanInitializeDefaultsHeaders()
    {
        $this->assertEquals("default", $this->sut->getMessageType());
    }

    public function testSutCanHandleMessageTypeHeader()
    {
        $this->assertEquals("default", $this->sut->getMessageType());
        $this->sut->setMessageType('doSomethingResponse');
        $this->assertEquals("doSomethingResponse", $this->sut->getMessageType());
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
        $sut = new Response(
            'this is a body string',
            ["content_type" => AbstractRequestResponseMessage::TEXT_CONTENT_TYPE]
        );
        $this->assertEquals("this is a body string", ($sut->toAmqpMessage())->getBody());
    }

    public function testSutCanHandleJsonContentType()
    {
        $sut = new Response(
            ["test" => "ok"],
            ["content_type" => AbstractRequestResponseMessage::JSON_CONTENT_TYPE]
        );
        $this->assertEquals('{"test":"ok"}', ($sut->toAmqpMessage())->getBody());
    }

    public function testSutCanHandleGzipContentType()
    {
        $sut = new Response(
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
                'message_id' => (Uuid::uuid4())->toString(),
                'type' => 'doSomethingResponse',
                'application_headers' => new AMQPTable([
                    'version' => '1.0.0',
                    'dateTime' => (new DateTime('now'))->format('Y-m-d H:i:s.v'),
                    'statusCode' => 401,
                    'statusMessage' => 'Unauthorized'
                ])
            ]
        ];
        $AMQPMessage = new AMQPMessage(...$message);
        $AMQPMessage->setConsumerTag("any#consume#tag");

        $sut = new Response(
            $AMQPMessage->getBody(),
            $AMQPMessage->get_properties(),
            $AMQPMessage->getConsumerTag()
        );

        $this->assertEquals("doSomethingResponse", $sut->getHeaders("type"));
        $this->assertIsObject($sut->getBody());
        $this->assertObjectHasAttribute("test", $sut->getBody());
        $this->assertEquals(
            AbstractRequestResponseMessage::JSON_CONTENT_TYPE,
            $sut->getHeaders("content_type")
        );
        $this->assertArrayHasKey("statusCode", $sut->getHeaders("application_headers"));
    }
}