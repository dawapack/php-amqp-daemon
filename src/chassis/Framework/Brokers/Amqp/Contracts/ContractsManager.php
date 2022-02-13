<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts;

use Closure;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannel;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\BrokerChannelsCollection;
use DaWaPack\Chassis\Framework\Brokers\Amqp\Contracts\Exceptions\ContractsValidatorException;
use DaWaPack\Chassis\Framework\Brokers\Exceptions\StreamerChannelNameNotFoundException;
use Symfony\Component\Yaml\Yaml;

use function DaWaPack\Chassis\Helpers\objectToArrayRecursive;

class ContractsManager implements ContractsManagerInterface
{
    public const OPERATION_PUBLISH = 'publish';
    public const OPERATION_SUBSCRIBE = 'subscribe';

    private BrokerConfiguration $brokerConfiguration;
    private ContractsValidator $validator;
    private array $channels = [];

    /**
     * @throws ContractsValidatorException
     */
    public function __construct(
        BrokerConfigurationInterface $brokerConfiguration,
        ContractsValidator $validator
    ) {
        $this->brokerConfiguration = $brokerConfiguration;
        $this->validator = $validator;
        $this->loadValidators();
        $this->validateInfrastructureFile();
    }

    /**
     * @inheritDoc
     */
    public function getChannel(string $channelName): ?BrokerChannel
    {
        return $this->channels[$channelName] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getChannels(): BrokerChannelsCollection
    {
        return new BrokerChannelsCollection(array_values($this->channels));
    }

    /**
     * @inheritDoc
     */
    public function toBasicPublishFunctionArguments(string $channelName, $data): array
    {
        if (is_null($this->getChannel($channelName))) {
            throw new StreamerChannelNameNotFoundException("channel name '$channelName' not found");
        }

        return [
            'message' => $data->toAmqpMessage(),
            'exchange' => $this->getChannel($channelName)->channelBindings->name,
            'routingKey' => $data->getRoutingKey(),
            'mandatory' => $this->getChannel($channelName)->operationBindings->mandatory,
            'immediate' => false,
            'ticket' => null
        ];
    }

    /**
     * @inheritDoc
     */
    public function toBasicConsumeFunctionArguments(string $channelName, Closure $callback): array
    {
        if (is_null($this->getChannel($channelName))) {
            throw new StreamerChannelNameNotFoundException("channel name '$channelName' not found");
        }

        return [
            'queue' => $this->getChannel($channelName)->channelBindings->name,
            'consumer_tag' => '',
            'no_local' => false,
            'no_ack' => !$this->getChannel($channelName)->operationBindings->ack,
            'exclusive' => false,
            'nowait' => false,
            'callback' => $callback,
            'ticket' => null,
            'arguments' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function toStreamConnectionFunctionArguments(): array
    {
        return $this->brokerConfiguration
            ->getConnectionConfiguration()
            ->toFunctionArguments(false);
    }

    /**
     * @inheritDoc
     */
    public function toLazyConnectionFunctionArguments(): array
    {
        return $this->brokerConfiguration
            ->getConnectionConfiguration()
            ->toLazyConnectionFunctionArguments(false);
    }

    /**
     * @throws ContractsValidatorException
     */
    private function validateInfrastructureFile(): void
    {
        $infrastructureFile = $this->parseYamlFile($this->getInfrastructureFileName());
        foreach ($infrastructureFile->channels as $channelName => $channelValues) {
            $this->validateBindingsAmqp($channelValues);
            $this->channels[$channelName] = new BrokerChannel(objectToArrayRecursive($channelValues));
        }
    }

    /**
     * @throws ContractsValidatorException
     */
    private function loadValidators(): void
    {
        $brokerContractConfiguration = $this->brokerConfiguration->getContractConfiguration();
        if (empty($brokerContractConfiguration->paths->validator)) {
            throw new ContractsValidatorException("validator path configuration cannot be empty");
        }
        $this->validator
            ->loadValidators($brokerContractConfiguration->paths->validator);
    }

    /**
     * @throws ContractsValidatorException
     */
    private function validateBindingsAmqp(object $channel): void
    {
        $operation = ($channel->bindings->amqp->is === "routingKey"
            ? self::OPERATION_PUBLISH
            : self::OPERATION_SUBSCRIBE
        );
        $this->validator
            ->validate(
                $channel->bindings->amqp,
                ContractsValidator::CHANNEL
            );
        $this->validator
            ->validate(
                $channel->{$operation}->bindings->amqp,
                ContractsValidator::OPERATION
            );
        $this->validator
            ->validate(
                $channel->{$operation}->message->bindings->amqp,
                ContractsValidator::MESSAGE
            );
    }

    private function parseYamlFile(string $filePath): object
    {
        return Yaml::parseFile($filePath, Yaml::PARSE_OBJECT_FOR_MAP);
    }

    private function getInfrastructureFileName(): string
    {
        $contractConfiguration = $this->brokerConfiguration->getContractConfiguration();
        return $contractConfiguration->paths->source
            . DIRECTORY_SEPARATOR
            . $contractConfiguration->definitions->infrastructure;
    }
}
