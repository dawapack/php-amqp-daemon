<?php

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;

class Record extends DataTransferObject
{
    // format 'YYYY-MM-DDTHH:II:SS.1234+HH:II'
    public string $timestamp;

    // Continent code (2 letters) 'eu', 'us', etc...
    public string $origin;

    // something like 'eu-central-1', etc...
    public string $region;

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\Application */
    public array $application;

    // Log level as string
    public string $level;

    // Log message
    public string $message = "";

    // application component snake_case
    public string $component;

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\Context */
    public array $context = [];

    /* @var \DaWaPack\Chassis\Classes\DTO\Logger\Extras */
    public array $extra = [];
}
