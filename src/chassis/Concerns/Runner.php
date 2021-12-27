<?php

namespace DaWaPack\Chassis\Concerns;

trait Runner
{

    /**
     * Is valid runner type?
     *
     * @return bool
     */
    private function isValidRunner(): bool
    {
        return in_array($this->runnerType, ['daemon', 'worker']);
    }
}
