<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

use DateTime;
use DaWaPack\Classes\Messages\DTO\RequestResponseHeaders;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Ramsey\Uuid\Uuid;

abstract class AbstractRequestResponseMessage
{
    public const DEFAULT_HEADER_PRIORITY = 0;
    public const DEFAULT_HEADER_TYPE = 'default';
    public const DEFAULT_HEADER_CONTENT_TYPE = 'application/json';
    public const DEFAULT_HEADER_CONTENT_ENCODING = 'UTF-8';
    public const DEFAULT_HEADER_VERSION = '1.0.0';
    public const DEFAULT_HEADER_DATETIME_FORMAT = 'Y-m-d H:i:s.v';

    public const TEXT_CONTENT_TYPE = 'text/plain';
    public const GZIP_CONTENT_TYPE = 'application/gzip';
    public const JSON_CONTENT_TYPE = 'application/json';
    public const MSGPACK_CONTENT_TYPE = 'application/msgpack';

    protected RequestResponseHeaders $headers;
    protected string $routingKey;
    protected string $exchange;
    protected string $queue;
    protected ?string $consumerTag;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @param mixed $body
     * @param array $headers
     * @param string|null $consumerTag
     */
    public function __construct(
        $body,
        array $headers = [],
        ?string $consumerTag = null
    ) {
        $this->consumerTag = $consumerTag;
        if (is_null($consumerTag)) {
            $this->headers = $this->fulfillHeaders($headers);
            $this->body = $body;
        } else {
            $this->headers = $this->decodeApplicationHeaders($headers);
            $this->body = $this->decodeBody($body);
        }
    }

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
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return $this
     * @var mixed $body
     *
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    public function toAmqpMessage(): AMQPMessage
    {
        $headers = $this->headers->toArray();
        if (!empty($headers["application_headers"])) {
            $headers["application_headers"] = new AMQPTable($headers["application_headers"]);
        }
        return new AMQPMessage($this->encodeBody(), $headers);
    }

    private function encodeBody(): string
    {
        $body = '';
        switch ($this->headers->content_type) {
            case self::TEXT_CONTENT_TYPE:
                $body = $this->body;
                break;
            case self::JSON_CONTENT_TYPE:
                $body = json_encode($this->body);
                break;
            case self::GZIP_CONTENT_TYPE:
                $body = base64_encode(gzcompress($this->body));
                break;
        }
        return $body;
    }

    private function decodeBody($body)
    {
        $decodedBody = $body;
        switch ($this->headers->content_type) {
            case self::JSON_CONTENT_TYPE:
                $decodedBody = json_decode($body);
                break;
            case self::GZIP_CONTENT_TYPE:
                $decodedBody = gzuncompress(base64_decode($body));
                break;
        }
        return $decodedBody;
    }

    /**
     * @param array $headers
     *
     * @return RequestResponseHeaders
     */
    private function fulfillHeaders(array $headers): RequestResponseHeaders
    {
        // content_type
        !isset($headers["content_type"]) && $this->setDefaultContentType($headers);
        // content_encoding
        !isset($headers["content_encoding"]) && $this->setDefaultContentEncoding($headers);
        // priority
        !isset($headers["priority"]) && $this->setDefaultPriority($headers);
        // correlation_id
        !isset($headers["correlation_id"]) && $this->setDefaultCorrelationId($headers);
        // message_id
        !isset($headers["message_id"]) && $this->setDefaultMessageId($headers);
        // type
        !isset($headers["type"]) && $this->setDefaultType($headers);
        // application_headers
        empty($headers["application_headers"]) && $this->setDefaultApplicationHeaders($headers);

        return new RequestResponseHeaders($headers);
    }

    /**
     * @param $headers
     *
     * @return RequestResponseHeaders
     */
    private function decodeApplicationHeaders($headers): RequestResponseHeaders
    {
        if (!empty($headers["application_headers"]) && $headers["application_headers"] instanceof AMQPTable) {
            $headers["application_headers"] = $headers["application_headers"]->getNativeData();
        }
        return new RequestResponseHeaders($headers);
    }

    private function setDefaultContentType(&$headers)
    {
        $headers["content_type"] = self::DEFAULT_HEADER_CONTENT_TYPE;
    }

    private function setDefaultContentEncoding(&$headers)
    {
        $headers["content_encoding"] = self::DEFAULT_HEADER_CONTENT_ENCODING;
    }

    private function setDefaultPriority(&$headers): void
    {
        $headers["priority"] = self::DEFAULT_HEADER_PRIORITY;
    }

    private function setDefaultCorrelationId(&$headers): void
    {
        $headers["correlation_id"] = (Uuid::uuid4())->toString();
    }

    private function setDefaultMessageId(&$headers): void
    {
        $headers["message_id"] = (Uuid::uuid4())->toString();
    }

    private function setDefaultType(&$headers): void
    {
        $headers["type"] = self::DEFAULT_HEADER_TYPE;
    }

    private function setDefaultApplicationHeaders(&$headers): void
    {
        $headers["application_headers"] = [];
        $headers["application_headers"]['version'] = self::DEFAULT_HEADER_VERSION;
        $headers["application_headers"]['dateTime'] = (new DateTime('now'))
            ->format(self::DEFAULT_HEADER_DATETIME_FORMAT);
    }
}
