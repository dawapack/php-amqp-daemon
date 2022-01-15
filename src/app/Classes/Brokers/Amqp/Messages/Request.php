<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Messages;

use DaWaPack\Classes\Brokers\Amqp\Messages\DTO\Headers;

class Request
{
    /**
     * @var Headers
     */
    protected Headers $headers;

    /**
     * @var mixed|null
     */
    protected $body = null;
}
