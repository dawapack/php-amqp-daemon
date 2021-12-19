<?php

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;

class ContextRequest extends DataTransferObject
{

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\Bindings */
    public array $bindings = [];

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\Message */
    public array $message = [];
}
