<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\MessageBags;

use DaWaPack\Classes\Brokers\Amqp\MessageBags\AbstractMessageBag;
use DaWaPack\Classes\Brokers\Amqp\MessageBags\DTO\BagProperties;
use DaWaPack\Tests\AppTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class AbstractMessageBagTest extends AppTestCase
{
    private AbstractMessageBag $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new class(
            ['test' => 'this is a body'],
            []
        ) extends AbstractMessageBag {
        };
    }

    /**
     * @return void
     */
    public function testSutIsInstanceOfAbstractMessageBag(): void
    {
        $this->assertInstanceOf(AbstractMessageBag::class, $this->sut);
    }

    /**
     * @return void
     */
    public function testSutCanInitializeProperties(): void
    {
        $this->assertEquals(
            AbstractMessageBag::JSON_CONTENT_TYPE,
            $this->sut->getProperty("content_type")
        );
    }

    /**
     * @return void
     */
    public function testSutCanReturnFulfilledBagProperties(): void
    {
        $bagProperties = $this->sut->getProperties();
        $this->assertInstanceOf(BagProperties::class, $bagProperties);
        $this->assertGreaterThanOrEqual(1, preg_match(
                '/\w{8}\-\w{4}\-\w{4}\-\w{4}\-\w{12}/', $bagProperties->correlation_id
            )
        );
    }

    /**
     * @return void
     */
    public function testSutCanUpdateBody(): void
    {
        $body = $this->sut->getBody();
        $this->assertIsArray($body);
        $this->assertArrayHasKey("test", $body);

        $this->sut->setBody(['modified_test' => 'this is a body']);

        $body = $this->sut->getBody();
        $this->assertIsArray($body);
        $this->assertArrayHasKey("modified_test", $body);
    }

    /**
     * @return void
     */
    public function testSutCanSetMessageTypeProperty(): void
    {
        $this->assertEquals("default", $this->sut->getProperty("type"));
        $this->sut->setMessageType("doSomethingNice");
        $this->assertEquals("doSomethingNice", $this->sut->getProperty("type"));
    }

    /**
     * @return void
     */
    public function testSutCanSetReplyToProperty(): void
    {
        $this->assertNull($this->sut->getProperty("reply_to"));
        $this->sut->setReplyTo("Any.Q.Responses");
        $this->assertEquals("Any.Q.Responses", $this->sut->getProperty("reply_to"));
    }

    /**
     * @return void
     */
    public function testSutCanSetRoutingKeyBinding(): void
    {
        $this->assertEmpty($this->sut->getRoutingKey());
        $this->sut->setRoutingKey("Any.RK.Responses");
        $this->assertEquals("Any.RK.Responses", $this->sut->getRoutingKey());
    }

    /**
     * @return void
     */
    public function testSutCanReturnBagAsAmqpMessageInstance(): void
    {
        $this->assertInstanceOf(AMQPMessage::class, $this->sut->toAmqpMessage());
    }

    /**
     * @param mixed $message
     * @param array $properties
     *
     * @dataProvider messageAndPropertiesDataProvider
     *
     * @return void
     */
    public function testSutCanBeInitializedByConsumedMessage($message, $properties): void
    {
        $AMQPMessage = new AMQPMessage($message, $properties);
        $AMQPMessage->setConsumerTag("any#consume#tag");

        $sut = new class (
            $AMQPMessage->getBody(),
            $AMQPMessage->get_properties(),
            $AMQPMessage->getConsumerTag()
        ) extends AbstractMessageBag {
        };

        $this->assertEquals("doSomething", $sut->getProperty("type"));
        $this->assertEquals("UTF-8", $sut->getProperty("content_encoding"));
    }

    /**
     * @return array[][]
     */
    public function messageAndPropertiesDataProvider(): array
    {
        return [
            [
                base64_encode(gzcompress('{"test":"ok"}')),
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                '{"test":"ok"}',
                [
                    'content_type' => AbstractMessageBag::JSON_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                'test ok',
                [
                    'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ]
        ];
    }
}
