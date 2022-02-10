<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BindingsInterface;
use Spatie\DataTransferObject\DataTransferObject;

class ChannelBindings extends DataTransferObject implements BindingsInterface
{
    public string $is;
    public string $name;
    public ?string $type = null;
    public bool $durable;
    public ?bool $exclusive = null;
    public bool $autoDelete;
    public string $vhost;

    /**
     * @inheritdoc
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($this->initParameters($parameters));
    }

    /**
     * @inheritDoc
     */
    public function toFunctionArguments(bool $onlyValues = true): array
    {
        return $this->is === "routingKey"
            ? $this->except("is", "exclusive", "vhost")->toArray()
            : $this->except("is", "type", "vhost")->toArray();
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function initParameters(array $parameters): array
    {
        if (empty($parameters)) {
            return [];
        }
        $is = $parameters['is'] === "routingKey" ? "exchange" : "queue";
        return array_merge($parameters[$is], ["is" => $parameters['is']]);
    }
}
