<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use Closure;
use DaWaPack\Classes\Brokers\Amqp\BrokerRequest;
use DaWaPack\Classes\Brokers\Amqp\BrokerResponse;
use DaWaPack\Classes\Brokers\Exceptions\StreamerChannelNameNotFoundException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class SubscriberStreamer extends AbstractStreamer implements SubscriberStreamerInterface
{
    private const QOS_PREFETCH_SIZE = 0;
    private const QOS_PREFETCH_COUNT = 1;
    // TODO: investigate this - set to true rise an error RabbitMQ side
    private const QOS_PER_CONSUMER = false;

    private AMQPChannel $streamChannel;
    private string $handler;
    private int $qosPrefetchSize;
    private int $qosPrefetchCount;
    private bool $qosPerConsumer;
    private bool $consumed = false;

    /**
     * @var BrokerRequest|BrokerResponse|null
     */
    private $data;

    /**
     * @inheritdoc
     */
    public function setHandler(string $handler): SubscriberStreamer
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHandler(): ?string
    {
        return $this->handler ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setQosPrefetchSize(int $qosPrefetchSize): SubscriberStreamer
    {
        $this->qosPrefetchSize = $qosPrefetchSize;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQosPrefetchSize(): ?int
    {
        return $this->qosPrefetchSize ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setQosPrefetchCount(int $qosPrefetchCount): SubscriberStreamer
    {
        $this->qosPrefetchCount = $qosPrefetchCount;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQosPrefetchCount(): ?int
    {
        return $this->qosPrefetchCount ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setQosPerConsumer(bool $qosPerConsumer): SubscriberStreamer
    {
        $this->qosPerConsumer = $qosPerConsumer;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isQosPerConsumer(): bool
    {
        return $this->qosPerConsumer;
    }

    public function consumed(): bool
    {
        return $this->consumed;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        $data = $this->data ?? null;
        if ($this->consumed()) {
            unset($this->data);
            $this->consumed = false;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function consume(?Closure $callback = null): SubscriberStreamer
    {
        // get a new channel
        $this->streamChannel = $this->getChannel();

        // set channel QOS
        $this->setStreamChannelQOS();

        // Consume
        $this->streamChannel->basic_consume(...$this->toFunctionArguments($callback));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function iterate(): void
    {
        try {
            $this->streamChannel->wait(null, false, 0.5);
        } catch (Throwable $reason) {
            // Rise this exception on timeout - this is a normal behaviour
        }
    }

    /**
     * @return void
     */
    protected function setStreamChannelQOS(): void
    {
        !isset($this->qosPrefetchSize) && $this->setDefaultQOSPrefetchSize();
        !isset($this->qosPrefetchCount) && $this->setDefaultQOSPrefetchCount();
        !isset($this->qosPerConsumer) && $this->setDefaultQOSPerConsumer();

        $this->streamChannel->basic_qos(
            $this->qosPrefetchSize,
            $this->qosPrefetchCount,
            $this->qosPerConsumer
        );
    }

    /**
     * @param AMQPMessage $message
     * @param bool $acknowledge
     * @param bool $holdData
     *
     * @return BrokerRequest|BrokerResponse
     */
    protected function handleData(AMQPMessage $message, bool $acknowledge = true, bool $holdData = false)
    {
        $data = new $this->handler(
            $message->getBody(),
            $message->get_properties(),
            $message->getConsumerTag()
        );

        if ($holdData === true) {
            $this->data = $data;
        }

        $acknowledge === true
            ? $message->ack()
            : $message->nack(true);

        $this->consumed = true;

        return $data;
    }

    /**
     * @return Closure
     */
    protected function getDefaultConsumerCallback(): Closure
    {
        $subscriber = $this;
        return function (AMQPMessage $message) use ($subscriber) {
            $subscriber->handleData($message, true, true);
        };
    }

    /**
     * @return void
     */
    private function setDefaultQOSPrefetchSize(): void
    {
        $this->qosPrefetchSize = self::QOS_PREFETCH_SIZE;
    }

    /**
     * @return void
     */
    private function setDefaultQOSPrefetchCount(): void
    {
        $this->qosPrefetchCount = self::QOS_PREFETCH_COUNT;
    }

    /**
     * @return void
     */
    private function setDefaultQOSPerConsumer(): void
    {
        $this->qosPerConsumer = self::QOS_PER_CONSUMER;
    }

    /**
     * @param Closure|null $callback
     *
     * @return array
     *
     * @throws StreamerChannelNameNotFoundException
     */
    private function toFunctionArguments(?Closure $callback): array
    {
        if (is_null($callback)) {
            $callback = $this->getDefaultConsumerCallback();
        }

        $channelName = $this->getChannelName() ?? "";

        return array_values(
            $this->contractsManager
                ->toBasicConsumeFunctionArguments($channelName, $callback)
        );
    }
}
