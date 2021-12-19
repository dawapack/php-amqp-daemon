<?php

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;

class Bindings extends DataTransferObject
{

    /**
     * @var string
     */
    public string $channelName;

    /**
     * @var array
     */
    public array $channelBindings;

    /**
     * @var array
     */
    public array $operationBindings;

    /**
     * @var array
     */
    public array $messageBindings;
}