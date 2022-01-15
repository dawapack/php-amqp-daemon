<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Contracts;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerChannel;
use DaWaPack\Classes\Brokers\Amqp\Contracts\Exceptions\ContractsValidatorException;
use Symfony\Component\Yaml\Yaml;
use function DaWaPack\Chassis\Helpers\objectToArrayRecursive;

class ContractsManager
{
    public const OPERATION_PUBLISH = 'publish';
    public const OPERATION_SUBSCRIBE = 'subscribe';

    private BrokerConfiguration $brokerConfiguration;
    private ContractsValidator $validator;
    private array $infrastructureChannels = [];

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

    public function getInfrastructureChannel(string $channelName): ?BrokerChannel
    {
        return $this->infrastructureChannels[$channelName] ?? null;
    }

    /**
     * @throws ContractsValidatorException
     */
    private function validateInfrastructureFile(): void
    {
        $infrastructureFile = $this->parseYamlFile($this->getInfrastructureFileName());
        foreach ($infrastructureFile->channels as $channelName => $channelValues) {
            $this->validateBindingsAmqp($channelValues);
            $this->infrastructureChannels[$channelName] = new BrokerChannel(objectToArrayRecursive($channelValues));
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
        $this->validator->loadValidators($brokerContractConfiguration->paths->validator);
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
