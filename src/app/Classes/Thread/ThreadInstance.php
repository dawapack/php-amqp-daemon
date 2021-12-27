<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Thread;

use DaWaPack\Classes\Thread\DTO\JobsProcessed;
use DaWaPack\Interfaces\ThreadInstanceInterface;

class ThreadInstance implements ThreadInstanceInterface
{

    /**
     * 'configuration', 'infrastructure' or 'worker'
     * @var string
     */
    private string $type;

    /**
     * @var JobsProcessed
     */
    private JobsProcessed $jobsProcessed;

    /**
     * @var float
     */
    private float $loadAverage = 0;
}
