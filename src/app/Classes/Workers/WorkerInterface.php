<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Workers;

interface WorkerInterface
{

    /**
     * @return void
     */
    public function start(): void;
}
