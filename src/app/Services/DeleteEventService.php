<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Brokers\Amqp\MessageBags\MessageBagInterface;
use Chassis\Framework\Services\ServiceInterface;
use function Chassis\Helpers\app;

class DeleteEventService implements ServiceInterface
{
    /**
     * Nobody cares about implementation
     *
     * @operation somethingDeleted
     *
     * @param MessageBagInterface $message
     *
     * @return void
     */
    public function __invoke(MessageBagInterface $message): void
    {
        app()->logger()->debug(
            "method __invoke of delete event service triggered",
            [
                "component" => "delete_event_service",
                "properties" => $message->getProperties(),
                "payload" => $message->getBody(),
            ]
        );
    }
}
