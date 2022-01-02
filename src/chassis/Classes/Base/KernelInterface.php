<?php
declare(strict_types=1);

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
     * @return void
     */
    public function boot(): void;
}
