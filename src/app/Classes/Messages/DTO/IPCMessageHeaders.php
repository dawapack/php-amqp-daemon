<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Messages\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class IPCMessageHeaders extends DataTransferObject
{
    /**
     * @var string
     */
    public string $method;

    /**
     * @var string|null
     */
    public ?string $source = null;

    /**
     * @var string|null
     */
    public string $encoding = 'array';
}
