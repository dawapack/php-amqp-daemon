<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Messages;

use DaWaPack\Classes\Brokers\Amqp\Messages\DTO\Headers;

class Response
{
    /**
     * @var Headers
     */
    protected Headers $headers;

    /**
     * @var mixed|null
     */
    protected $body = null;

    /**
     * @param string|null $key
     *
     * @return mixed|array|null
     */
    public function getHeaders(?string $key = null)
    {
        return !is_null($key)
            ? $this->headers->{$key} ?? null
            : $this->headers->toArray();
    }

    /**
     * @return mixed|null
     */
    public function getBody()
    {
        return $this->body;
    }
}
