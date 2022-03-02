<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Application;
use Chassis\Framework\Brokers\Amqp\MessageBags\MessageBagInterface;
use Chassis\Framework\Services\ServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DeleteEventService implements ServiceInterface
{
    /**
     * Nobody cares about implementation
     *
     * @operation somethingDeleted
     *
     * @param MessageBagInterface $message
     * @param Application $application
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(MessageBagInterface $message, Application $application): void
    {
        $application->logger()->debug(
            "method __invoke of delete event service triggered",
            [
                "component" => "delete_event_service",
                "properties" => $message->getProperties(),
                "payload" => $message->getBody(),
            ]
        );
    }
}
