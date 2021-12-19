<?php

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;

class Extras extends DataTransferObject
{

    /**
     * @var string
     */
    public string $organisationId;

    /**
     * @var string
     */
    public string $userId;

    /**
     * @var string
     */
    public string $formId;

    /**
     * @var string
     */
    public string $correlationId;

    /**
     * @var string
     */
    public string $jobId;
}
