<?php
declare(strict_types=1);

namespace DaWaPack\Tests;

use DaWaPack\Chassis\Application;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannel;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannelsCollection;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\OperationBindings;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManagerInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase
{
    protected bool $infrastructureDeclare = false;
    protected Application $app;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->app = Application::getInstance();
        // need infrastructure?
        if ($this->infrastructureDeclare === true) {
            $streamConnection = $this->app->getnew('broker-streamer');
            $this->declareExchanges($streamConnection);
            $this->declareQueues($streamConnection);
        }

        parent::__construct($name, $data, $dataName);
    }

    protected function declareExchanges(AMQPStreamConnection $streamConnection): void
    {
        if ($this->app->has('broker-declared-exchanges')) {
            return;
        }
        /** @var ContractsManager $contractManager */
        $contractManager = $this->app->get(ContractsManagerInterface::class);
        $channels = $contractManager->getChannels();
        $streamChannel = $streamConnection->channel();
        $exchangesDeclared = [];
        /**
         * @var BrokerChannelsCollection $channels
         * @var BrokerChannel $channel
         */
        foreach ($channels as $channel) {
            if ($channel->channelBindings->is !== "routingKey") {
                continue;
            }
            $exchangesDeclared[$channel->channelBindings->name] = $channel->channelBindings
                ->toFunctionArguments(false);
            // declare exchange
            $functionArguments = array_merge(
                [
                    'name' => null,
                    'type' => null,
                    'passive' => false,
                    'durable' => false,
                    'autoDelete' => true,
                    'internal' => false,
                    'nowait' => false,
                    'arguments' => [],
                    'ticket' => null
                ],
                $exchangesDeclared[$channel->channelBindings->name]
            );
            $streamChannel->exchange_declare(...array_values($functionArguments));
        }
        $this->app->add('broker-declared-exchanges', $exchangesDeclared);
    }

    protected function declareQueues(AMQPStreamConnection $streamConnection): void
    {
        if ($this->app->has('broker-declared-queues')) {
            return;
        }
        /** @var \DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\ContractsManager $contractManager */
        $contractManager = $this->app->get(ContractsManagerInterface::class);
        $channels = $contractManager->getChannels();
        $streamChannel = $streamConnection->channel();
        $queuesDeclared = [];
        /**
         * @var BrokerChannelsCollection $channels
         * @var BrokerChannel $channel
         */
        foreach ($channels as $channel) {
            if ($channel->channelBindings->is !== "queue") {
                continue;
            }
            $queuesDeclared[$channel->channelBindings->name] = $channel->channelBindings
                ->toFunctionArguments(false);
            // declare queue
            $functionArguments = array_merge(
                [
                    'name' => null,
                    'passive' => false,
                    'durable' => false,
                    'exclusive' => false,
                    'autoDelete' => true,
                    'nowait' => false,
                    'arguments' => [],
                    'ticket' => null
                ],
                $queuesDeclared[$channel->channelBindings->name]
            );
            $streamChannel->queue_declare(...array_values($functionArguments));
            $this->bindQueue($streamChannel, $channel->operationBindings, $channel->channelBindings->name);
        }
        $this->app->add('broker-declared-queues', $queuesDeclared);
    }

    protected function bindQueue(AMQPChannel $channel, OperationBindings $operationBindings, string $queueName): void
    {
        if (empty($operationBindings->cc)) {
            return;
        }
        foreach ($operationBindings->cc as $routingKey) {
            $functionArguments = array_merge(
                [$queueName],
                explode("|", $routingKey)
            );
            if (count($functionArguments) != 3) {
                continue;
            }
            $channel->queue_bind(...$functionArguments);
        }

    }
}
