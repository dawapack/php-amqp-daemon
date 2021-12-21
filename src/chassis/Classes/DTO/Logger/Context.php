<?php

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;

class Context extends DataTransferObject
{

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\ContextError */
    public array $error;

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\ContextRequest */
    public array $request;

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\ContextResponse */
    public array $response;
}
