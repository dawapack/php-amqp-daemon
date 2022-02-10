<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\MessageBags;

use DaWaPack\Classes\Brokers\Amqp\MessageBags\AbstractMessageBag;
use DaWaPack\Classes\Brokers\Amqp\MessageBags\DTO\BagBindings;
use DaWaPack\Classes\Brokers\Amqp\MessageBags\DTO\BagProperties;
use DaWaPack\Classes\Brokers\Exceptions\MessageBagFormatException;
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
     * @dataProvider consumedMessageAndPropertiesDataProvider
     *
     * @return void
     */
    public function testSutCanBeInitializedByConsumedMessage($message, array $properties): void
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
    public function consumedMessageAndPropertiesDataProvider(): array
    {
        return [
            [
                base64_encode(gzcompress('{"test":"ok"}')),
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
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
                    'delivery_mode' => 2,
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
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ]
        ];
    }

    /**
     * @param mixed $message
     * @param array $properties
     *
     * @dataProvider messageAndPropertiesDataProvider
     *
     * @return void
     */
    public function testSutCanBeInitialized($message, array $properties): void
    {
        $sut = new class ($message, $properties) extends AbstractMessageBag {
        };
        $properties = $sut->toAmqpMessage()->get_properties();
        $this->assertEquals("doSomething", $properties["type"]);
        $this->assertEquals("UTF-8", $properties["content_encoding"]);
    }

    /**
     * @return array[][]
     */
    public function messageAndPropertiesDataProvider(): array
    {
        return [
            [
                "gzip this string",
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                ["gzip" => "this array"],
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                (object)["gzip" => "this object"],
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                ["json" => "encode this array"],
                [
                    'content_type' => AbstractMessageBag::JSON_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                (object)["json" => "encode this object"],
                [
                    'content_type' => AbstractMessageBag::JSON_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
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
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ]
        ];
    }

    /**
     * @return void
     */
    public function testSutCanSetTheChannelName(): void
    {
        $message = 'test ok';
        $properties = [
            'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
            'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
            'priority' => 0,
            'delivery_mode' => 2,
            'correlation_id' => (Uuid::uuid4())->toString(),
            'message_id' => (Uuid::uuid4())->toString(),
            'type' => 'doSomething',
        ];

        $sut = new class ($message, $properties) extends AbstractMessageBag {
        };

        $sut->setChannelName('my/channel/name');
        $this->assertEquals('my/channel/name', $sut->getBinding('channelName'));
    }

    /**
     * @return void
     */
    public function testSutCanSetTheExchangeName(): void
    {
        $message = 'test ok';
        $properties = [
            'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
            'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
            'priority' => 0,
            'delivery_mode' => 2,
            'correlation_id' => (Uuid::uuid4())->toString(),
            'message_id' => (Uuid::uuid4())->toString(),
            'type' => 'doSomething',
        ];

        $sut = new class ($message, $properties) extends AbstractMessageBag {
        };

        $sut->setExchangeName('my_exchange_name');
        $this->assertEquals('my_exchange_name', $sut->getBinding('exchange'));
    }

    /**
     * @return void
     */
    public function testSutCanSetTheQueueName(): void
    {
        $message = 'test ok';
        $properties = [
            'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
            'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
            'priority' => 0,
            'delivery_mode' => 2,
            'correlation_id' => (Uuid::uuid4())->toString(),
            'message_id' => (Uuid::uuid4())->toString(),
            'type' => 'doSomething',
        ];

        $sut = new class ($message, $properties) extends AbstractMessageBag {
        };

        $sut->setQueueName('my_queue_name');
        $this->assertEquals('my_queue_name', $sut->getBinding('queue'));
    }

    /**
     * @return void
     */
    public function testSutCanGetBagBindings(): void
    {
        $message = 'test ok';
        $properties = [
            'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
            'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
            'priority' => 0,
            'delivery_mode' => 2,
            'correlation_id' => (Uuid::uuid4())->toString(),
            'message_id' => (Uuid::uuid4())->toString(),
            'type' => 'doSomething',
        ];

        $sut = new class ($message, $properties) extends AbstractMessageBag {
        };

        $this->assertInstanceOf(BagBindings::class, $sut->getBindings());
    }

    /**
     * @return void
     */
    public function testSutCanGetBody(): void
    {
        $message = 'testSutCanGetBody';
        $properties = [
            'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
            'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
            'priority' => 0,
            'delivery_mode' => 2,
            'correlation_id' => (Uuid::uuid4())->toString(),
            'message_id' => (Uuid::uuid4())->toString(),
            'type' => 'doSomething',
        ];

        $sut = new class ($message, $properties) extends AbstractMessageBag {
        };

        $this->assertEquals($message, $sut->getBody());
    }

    /**
     * @param mixed $message
     * @param array $properties
     *
     * @dataProvider wrongConsumedMessageAndPropertiesDataProvider
     *
     * @return void
     */
    public function testSutInitializationByConsumedMessageMustFailWithMessageBagFormatException($message, array $properties): void
    {
        $this->expectException(MessageBagFormatException::class);
        $AMQPMessage = new AMQPMessage($message, $properties);
        $AMQPMessage->setConsumerTag("any#consume#tag");

        new class (
            $AMQPMessage->getBody(),
            $AMQPMessage->get_properties(),
            $AMQPMessage->getConsumerTag()
        ) extends AbstractMessageBag {
        };

    }

    /**
     * @return array[][]
     */
    public function wrongConsumedMessageAndPropertiesDataProvider(): array
    {
        return [
            [
                ["test" => __METHOD__],
                [
                    'content_type' => 'unknown',
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                ["test" => __METHOD__],
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                ["test" => __METHOD__],
                [
                    'content_type' => AbstractMessageBag::JSON_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                ["test" => __METHOD__],
                [
                    'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ]
        ];
    }

    /**
     * @param mixed $message
     * @param array $properties
     *
     * @dataProvider wrongMessageAndPropertiesDataProvider
     *
     * @return void
     */
    public function testSutMustFailWithMessageBagFormatExceptionWhenTryingToConvertBagToAmqpMessage(
        $message, array $properties
    ): void {
        $this->expectException(MessageBagFormatException::class);
        $sut = new class($message, $properties) extends AbstractMessageBag {
        };
        $sut->toAmqpMessage();

    }

    /**
     * @return array[][]
     */
    public function wrongMessageAndPropertiesDataProvider(): array
    {
        return [
            [
                ["test" => __METHOD__],
                [
                    'content_type' => 'unknown',
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                false,
                [
                    'content_type' => AbstractMessageBag::GZIP_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                10,
                [
                    'content_type' => AbstractMessageBag::JSON_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ],
            [
                ["test" => __METHOD__],
                [
                    'content_type' => AbstractMessageBag::TEXT_CONTENT_TYPE,
                    'content_encoding' => AbstractMessageBag::DEFAULT_CONTENT_ENCODING,
                    'priority' => 0,
                    'delivery_mode' => 2,
                    'correlation_id' => (Uuid::uuid4())->toString(),
                    'message_id' => (Uuid::uuid4())->toString(),
                    'type' => 'doSomething',
                ]
            ]
        ];
    }
}
