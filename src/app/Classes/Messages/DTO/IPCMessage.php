<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class IPCMessage extends DataTransferObject
{

    /**
     * @var \DaWaPack\Classes\Messages\DTO\IPCMessageHeaders
     */
    public IPCMessageHeaders $headers;

    /**
     * @var mixed|null
     */
    public $body = null;
}
