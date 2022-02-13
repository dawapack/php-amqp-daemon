<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Logger\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;

class Context extends DataTransferObject
{
    /* @var \DaWaPack\Chassis\Framework\Logger\DataTransferObject\ContextError */
    public ?ContextError $error;
}
