<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class RequestResponseHeaders extends DataTransferObject
{
    /**
     * @example 'text/plain', 'application/json', 'application/gzip'
     * @var string|null
     */
    public ?string $content_type;

    /**
     * @example 'UTF-8', 'ISO...'
     * @var string|null
     */
    public ?string $content_encoding;

    /**
     * @description 0 to 10
     * @var int
     */
    public int $priority;

    /**
     * @format UUID
     * @var string
     */
    public string $correlation_id;

    /**
     * @var string|null
     */
    public ?string $reply_to;

    /**
     * @example timestamp + X seconds
     * @var int|null
     */
    public ?int $expiration;

    /**
     * @format UUID
     * @var string
     */
    public string $message_id;

    /**
     * @example timestamp
     * @var int|null
     */
    public ?int $timestamp;

    /**
     * @example message type discriminator like 'user.created'
     * @var string
     */
    public string $type;

    /**
     * @var string|null
     */
    public ?string $user_id;

    /**
     * @example 'my-application-name'
     * @var string|null
     */
    public ?string $app_id;

    /**
     * @var array
     */
    public array $application_headers = [];
}
