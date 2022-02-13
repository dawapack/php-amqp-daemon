<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject;

use DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\BindingsInterface;
use Spatie\DataTransferObject\DataTransferObject;

class MessageBindings extends DataTransferObject implements BindingsInterface
{
    public string $contentEncoding;
    public string $messageType;

    /**
     * @inheritDoc
     */
    public function toFunctionArguments(bool $onlyValues = true): array
    {
        return $this->toArray();
    }
}
