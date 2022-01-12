<?php

namespace DaWaPack\Classes\Brokers\Contracts;

use DaWaPack\Classes\Brokers\Configuration\DTO\BrokerContract;
use DaWaPack\Interfaces\BrokerConfigurationInterface;
use Symfony\Component\Yaml\Yaml;

class ContractsManager
{
    public const OPERATION_PUBLISH = 'publish';
    public const OPERATION_SUBSCRIBE = 'subscribe';

    private BrokerContract $brokerContractConfiguration;
    private ContractsValidator $validator;

    public function __construct(BrokerConfigurationInterface $brokerConfiguration, ContractsValidator $validator)
    {
        $this->brokerContractConfiguration = $brokerConfiguration->getContractConfiguration();
        $this->validator = $validator;

        if (!empty($this->brokerContractConfiguration->path->validator)) {
            $this->validator->loadValidators($this->brokerContractConfiguration->path->validator);
        }
    }

    /**
     * @throws Exceptions\ContractsValidatorException
     */
    public function init(): void
    {
        $this->validateInfrastructureFile();
    }

    /**
     * @throws Exceptions\ContractsValidatorException
     */
    public function validateInfrastructureFile(): void
    {
        $infrastructureFile = $this->parseYamlFile($this->getInfrastructureFileName());
        foreach ($infrastructureFile->channels as $channel) {
            $this->validateBindingsAmqp($channel);
        }
    }

    /**
     * @throws Exceptions\ContractsValidatorException
     */
    private function validateBindingsAmqp(object $channel): void
    {
        $operation = isset($channel->publish) ? self::OPERATION_PUBLISH : self::OPERATION_SUBSCRIBE;
        $this->validator->validate($channel->bindings->amqp, ContractsValidator::CHANNEL);
        $this->validator->validate($channel->{$operation}->bindings->amqp, ContractsValidator::OPERATION);
        $this->validator->validate($channel->{$operation}->message->bindings->amqp, ContractsValidator::MESSAGE);
    }

    private function parseYamlFile(string $filePath): object
    {
        return Yaml::parseFile($filePath, Yaml::PARSE_OBJECT_FOR_MAP);
    }

    private function getInfrastructureFileName(): string
    {
        return $this->brokerContractConfiguration->path->source . DIRECTORY_SEPARATOR . $this->brokerContractConfiguration->definitions->infrastructure;
    }
}