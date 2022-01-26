<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Threads;

use DaWaPack\Classes\Threads\Configuration\ThreadConfiguration;
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
    public function getOutgoingChannel(): Channel;

    /**
     * @return Channel
     */
    public function getIncomingChannel(): Channel;
}
