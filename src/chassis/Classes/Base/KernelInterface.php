<?php

namespace DaWaPack\Chassis\Classes\Base;

use DaWaPack\Chassis\Application;
use Psr\Log\LoggerInterface;

interface KernelInterface
{

    /**
     * @return Application
     */
    public function app(): Application;

    /**
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface;

    /**
     * @param string|null $threadId
     *
     * @return void
     */
    public function boot(?string $threadId = null): void;
}
