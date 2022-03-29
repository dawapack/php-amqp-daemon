<?php

namespace DaWaPack\Message;

use Chassis\Framework\Adapters\Message\ApplicationMessageInterface;
use Chassis\Framework\Services\AbstractService;

class ApplicationMessage implements ApplicationMessageInterface
{
    private array $headers;
    private array $payload = AbstractService::DEFAULT_PAYLOAD;

    /**
     * @param array $body
     * @param array $headers
     */
    public function __construct(array $body, array $headers = [])
    {
        $this->payload["items"] = [$body];
        $this->headers = $headers;
    }

    /**
     * @param $metaData
     *
     * @return $this
     */
    public function setMetaData($metaData): ApplicationMessage
    {
        $this->payload["meta"] = $metaData;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHeader(string $name, $value): ApplicationMessage
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}