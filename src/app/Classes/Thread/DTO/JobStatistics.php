<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Thread\DTO;

use DateTimeImmutable;
use Spatie\DataTransferObject\DataTransferObject;

class JobStatistics extends DataTransferObject
{

    /**
     * @var string
     */
    public string $jobId;

    /**
     * @var string
     */
    public string $messageId;

    /**
     * @var string
     */
    public string $channelName;

    /**
     * @var string
     */
    public string $messageType;

    /**
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $dateTimeStart;

    /**
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $dateTimeEnd;
}
