<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerChannel;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\ChannelBindings;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\OperationBindings;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\Heartbeat\PCNTLHeartbeatSender;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Wire\AMQPTable;
use Throwable;

abstract class AbstractStreamer implements StreamerInterface
{

    protected AMQPStreamConnection $streamerConnection;
    protected PCNTLHeartbeatSender $heartbeatSender;
    protected ?string $channelName;
    protected ?string $operation;
    protected array $exchangeDeclareMapper = [
        'name' => null,
        'type' => null,
        'passive' => true,
        'durable' => false,
        'autoDelete' => true,
        'internal' => false,
        'nowait' => false,
        'arguments' => array(),
        'ticket' => null
    ];
    protected array $queueDeclareMapper = [
        'name' => null,
        'passive' => true,
        'durable' => false,
        'exclusive' => false,
        'autoDelete' => true,
        'nowait' => false,
        'arguments' => [
            'x-max-priority' => 5,
        ],
        'ticket' => null
    ];
    protected array $availableChannels;

    /**
     * AbstractStreamer constructor.
     *
     * @param AMQPStreamConnection $streamerConnection
     * @param string|null $channelName
     * @param string|null $operation
     */
    public function __construct(
        AMQPStreamConnection $streamerConnection,
        ?string $channelName = null,
        ?string $operation = null
    ) {
        $this->channelName = $channelName;
        $this->operation = $operation;
        $this->streamerConnection = $streamerConnection;
        // enable heartbeat?
        if ($streamerConnection->getHeartbeat() > 0) {
            $this->heartbeatSender = new PCNTLHeartbeatSender($streamerConnection);
            $this->heartbeatSender->register();
        }
        // arguments transformation - AMQPTable format is required
        $this->queueDeclareMapper["arguments"] = new AMQPTable($this->queueDeclareMapper["arguments"]);
        $this->exchangeDeclareMapper["arguments"] = new AMQPTable($this->exchangeDeclareMapper["arguments"]);
        // declare root available channels structure
        $this->availableChannels = [
            'exchanges' => [],
            'queues' => [],
        ];
    }

    /**
     * AbstractStreamer destructor.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @inheritDoc
     */
    public function getChannel(?int $id = null): AMQPChannel
    {
        // create new channel using the given stream connection
        return $this->streamerConnection->channel($id);
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): bool
    {
        try {
            if (!$this->streamerConnection->isConnected()) {
                throw new AMQPConnectionClosedException();
            }
            if (isset($this->heartbeatSender)) {
                $this->heartbeatSender->unregister();
                unset($this->heartbeatSender);
            }
            if (isset($this->streamerConnection)) {
                $this->streamerConnection->close();
            }
        } catch (Throwable $reason) {
            // Fault-tolerant
            return false;
        }
        return true;
    }

    protected function channelDeclare(BrokerChannel $brokerChannel, bool $declareBindings): void
    {
        // exchange declaration
        if ($brokerChannel->channelBindings->is === "routingKey") {
            $this->exchangeDeclare($brokerChannel->channelBindings);
            return;
        }
        // queue declaration
        $this->queueDeclare($brokerChannel->channelBindings);
        if (count($brokerChannel->operationBindings->cc) > 0 && $declareBindings) {
            $this->channelBind($brokerChannel->operationBindings, $brokerChannel->channelBindings->name);
        }
    }

    protected function channelDelete(BrokerChannel $brokerChannel): void
    {
        // exchange deletion
        if ($brokerChannel->channelBindings->is === "routingKey") {
            $this->exchangeDelete($brokerChannel->channelBindings);
            return;
        }
        // queue & bindings deletion
        $this->queueDelete($brokerChannel->channelBindings);
    }

    protected function exchangeDeclare(ChannelBindings $channelBindings): void
    {
        $channel = $this->getChannel();
        try {
            $functionArguments = array_merge(
                $this->exchangeDeclareMapper, $channelBindings->toFunctionArguments(false)
            );
            // will throw an exception if the exchange doesn't exist - passive = true
            $channel->exchange_declare(...array_values($functionArguments));
        } catch (AMQPProtocolChannelException $reason) {
            // force exchange declaration
            $functionArguments = array_merge(
                $this->exchangeDeclareMapper,
                $channelBindings->toFunctionArguments(false),
                ['passive' => false]
            );
            $channel = $this->getChannel();
            $channel->exchange_declare(...array_values($functionArguments));
        }
        $this->availableChannels["exchanges"][$functionArguments["name"]] = $functionArguments;
        $channel->close();
    }

    protected function exchangeDelete(ChannelBindings $channelBindings)
    {
        $functionArguments = array_intersect_key(
            $channelBindings->toFunctionArguments(false), ['name' => null]
        );
        $channel = $this->getChannel();
        $channel->exchange_delete(...array_values($functionArguments));
        unset($this->availableChannels["exchanges"][$functionArguments["name"]]);
        $channel->close();
    }

    protected function queueDeclare(ChannelBindings $channelBindings): void
    {
        $channel = $this->getChannel();
        try {
            $functionArguments = array_merge(
                $this->queueDeclareMapper, $channelBindings->toFunctionArguments(false)
            );
            // will throw an exception if the queue doesn't exist - passive = true
            $channel->queue_declare(...array_values($functionArguments));
        } catch (AMQPProtocolChannelException $reason) {
            // force exchange declaration
            $functionArguments = array_merge(
                $this->queueDeclareMapper,
                $channelBindings->toFunctionArguments(false),
                ['passive' => false]
            );
            $channel = $this->getChannel();
            $channel->queue_declare(...array_values($functionArguments));
        }
        $this->availableChannels["queues"][$functionArguments["name"]] = $functionArguments;
        $channel->close();
    }

    protected function queueDelete(ChannelBindings $channelBindings)
    {
        $functionArguments = array_intersect_key(
            $channelBindings->toFunctionArguments(false), ['name' => null]
        );
        $channel = $this->getChannel();
        $channel->queue_delete(...array_values($functionArguments));
        unset($this->availableChannels["queues"][$functionArguments["name"]]);
        $channel->close();
    }

    protected function channelBind(OperationBindings $operationBindings, string $queue): void
    {
        foreach ($operationBindings->cc as $routingKey) {
            $functionArguments = array_merge(
                [$queue],
                explode("|", $routingKey)
            );
            if (count($functionArguments) != 3) {
                continue;
            }
            $channel = $this->getChannel();
            $channel->queue_bind(...$functionArguments);
            $channel->close();
        }
    }
}
