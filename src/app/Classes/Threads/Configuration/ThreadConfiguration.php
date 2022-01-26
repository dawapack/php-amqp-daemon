<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Threads\Configuration;

use Spatie\DataTransferObject\DataTransferObject;

class ThreadConfiguration extends DataTransferObject
{

    /**
     * @var string
     */
    public string $threadType;

    /**
     * @var string
     */
    public string $handler;

    /**
     * @var int
     */
    public int $minimum;

    /**
     * @var int
     */
    public int $maximum;

    /**
     * @var array
     */
    public array $triggers;

    /**
     * @var int
     */
    public int $ttl;

    /**
     * @var int
     */
    public int $maxJobs;

    /**
     * @var array
     */
    public array $channels;

    /**
     * @var string
     */
    public string $channelName;

    /**
     * @var bool
     */
    public bool $enabled;
}
