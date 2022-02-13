<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Threads;

use DaWaPack\Chassis\Framework\Threads\Configuration\ThreadConfiguration;
use parallel\Channel;
use parallel\Future;

interface ThreadInstanceInterface
{
    /**
     * @param ThreadConfiguration $threadConfiguration
     *
     * @return void
     */
    public function setConfiguration(ThreadConfiguration $threadConfiguration): void;

    /**
     * @param string|null $key
     *
     * @return mixed
     */
    public function getConfiguration(?string $key = null);

    /**
     * @return Future
     */
    public function getFuture(): Future;

    /**
     * @return Channel
     */
    public function getWorkerChannel(): Channel;

    /**
     * @return Channel
     */
    public function getThreadChannel(): Channel;
}
