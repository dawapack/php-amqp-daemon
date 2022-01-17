<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

use DaWaPack\Classes\Messages\DTO\RequestResponseHeaders;

class Response
{
    /**
     * @var \DaWaPack\Classes\Messages\DTO\RequestResponseHeaders
     */
    protected RequestResponseHeaders $headers;

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
