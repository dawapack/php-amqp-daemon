<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class BrokerChannel extends DataTransferObject
{
    public ChannelBindings $channelBindings;
    public OperationBindings $operationBindings;
    public MessageBindings $messageBindings;

    /**
     * @inheritDoc
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($this->bindingsFormatter($parameters));
    }

    private function bindingsFormatter(array $parameters): array
    {
        $operation = $parameters["bindings"]["amqp"]["is"] === "routingKey" ? "publish" : "subscribe";
        return [
            "channelBindings" => $parameters["bindings"]["amqp"] ?? [],
            "operationBindings" => $parameters[$operation]["bindings"]["amqp"],
            "messageBindings" => $parameters[$operation]["message"]["bindings"]["amqp"],
        ];
    }
}
