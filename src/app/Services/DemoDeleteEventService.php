<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Adapters\Message\InboundMessageInterface;
use Chassis\Framework\Logger\Logger;

class DemoDeleteEventService
{
    /**
     * Nobody cares about implementation
     *
     * @operation somethingDeleted
     *
     * @param InboundMessageInterface $message
     *
     * @return void
     */
    public function __invoke(InboundMessageInterface $message): void
    {
        Logger::info(
            "got event - DemoDeleteEventService::__invoke()",
            [
                "component" => "application_info",
                "message" => [
                    'properties' => $message->getProperties(),
                    'headers' => $message->getHeaders(),
                    'body' => $message->getBody(),
                ]
            ]
        );
    }
}
