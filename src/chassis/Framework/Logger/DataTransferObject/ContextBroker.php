<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Logger\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;

class ContextBroker extends DataTransferObject
{
    public string $channelName;

    /* @var \DaWaPack\Chassis\Framework\Logger\DataTransferObject\Bindings */
    public Bindings $bindings;

    /* @var \DaWaPack\Chassis\Framework\Logger\DataTransferObject\Message */
    public Message $message;
}
