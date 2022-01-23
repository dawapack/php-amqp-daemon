<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

interface RequestResponseInterface
{
    /**
     * Send this request through a given channel
     *
     * @param ?string $channelName
     *
     * @return void
     */
    public function send(?string $channelName = null): void;
}
