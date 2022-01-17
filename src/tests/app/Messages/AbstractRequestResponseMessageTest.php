<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Messages;

use DateTime;
use DaWaPack\Classes\Messages\AbstractRequestResponseMessage;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Ramsey\Uuid\Uuid;

class AbstractRequestResponseMessageTest extends AppTestCase
{
    private AbstractRequestResponseMessage $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new class(
            'this is a string body',
            [
                "content_type" => AbstractRequestResponseMessage::TEXT_CONTENT_TYPE,
                "content_encoding" => "UTF-8"
            ]
        ) extends AbstractRequestResponseMessage {
        };
    }

    public function testSutIsInstanceOfAbstractRequestResponseMessage()
    {
        $this->assertInstanceOf(AbstractRequestResponseMessage::class, $this->sut);
    }

    public function testSutCanInitializeDefaultsHeaders()
    {
        $this->assertEquals("default", $this->sut->getHeaders("type"));
    }

    public function testSutCanInitializeDefaultsAndGivenHeaders()
    {
        $this->assertEquals("default", $this->sut->getHeaders("type"));
        $this->assertEquals("UTF-8", $this->sut->getHeaders("content_encoding"));
        $this->assertEquals(
            AbstractRequestResponseMessage::TEXT_CONTENT_TYPE,
            $this->sut->getHeaders("content_type")
        );
    }

    public function testSutCanAccessApplicationHeaders()
    {
        $this->assertIsArray($this->sut->getHeaders("application_headers"));
    }

    public function testSutCanBeInitializedByAConsumedMessage()
    {
        $message = [
            base64_encode(gzcompress('{"test":"ok"}')),
            [
                'content_type' => AbstractRequestResponseMessage::GZIP_CONTENT_TYPE,
                'content_encoding' => AbstractRequestResponseMessage::DEFAULT_HEADER_CONTENT_ENCODING,
                'priority' => 0,
                'correlation_id' => (Uuid::uuid4())->toString(),
                'reply_to' => 'DaWaPack.Q.Responses',
                'message_id' => (Uuid::uuid4())->toString(),
                'type' => 'doSomething',
                'application_headers' => new AMQPTable([
                    'version' => '1.0.0',
                    'dateTime' => (new DateTime('now'))->format('Y-m-d H:i:s.v')
                ])
            ]
        ];
        $AMQPMessage = new AMQPMessage(...$message);
        $AMQPMessage->setConsumerTag("any#consume#tag");

        $sut = new class (
            $AMQPMessage->getBody(),
            $AMQPMessage->get_properties(),
            $AMQPMessage->getConsumerTag()
        ) extends AbstractRequestResponseMessage {
        };

        $this->assertEquals("doSomething", $sut->getHeaders("type"));
        $this->assertEquals("UTF-8", $sut->getHeaders("content_encoding"));
    }
}
