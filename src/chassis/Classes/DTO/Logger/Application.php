<?php
declare(strict_types=1);

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;

class Application extends DataTransferObject
{

    // Application name
    public string $name;

    // Application environment
    public string $environment;

    // Application type
    public string $type;
}
