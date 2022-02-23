<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Brokers\Amqp\MessageBags\MessageBagInterface;
use Chassis\Framework\Services\ServiceInterface;

class ClearTrackerService implements ServiceInterface
{
    /**
     * Nobody cares about implementation
     *
     * @operation clearTracker
     *
     * @param MessageBagInterface $message
     *
     * @return void
     */
    public function __invoke(MessageBagInterface $message): void
    {
//        $jobId = $message->getProperty("application_headers")["jobId"];
//        var_dump("[$jobId] " . __METHOD__);
    }
}
