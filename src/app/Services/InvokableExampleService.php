<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use DaWaPack\Chassis\Framework\Brokers\Amqp\BrokerResponse;
use DaWaPack\Chassis\Framework\Brokers\Amqp\MessageBags\MessageBagInterface;
use DaWaPack\Chassis\Framework\Services\ServiceInterface;

use function DaWaPack\Chassis\Helpers\app;

class InvokableExampleService implements ServiceInterface
{
    /**
     * Nobody cares about implementation
     *
     * @param MessageBagInterface $messageBag
     *
     * @return BrokerResponse|void
     */
    public function __invoke(MessageBagInterface $messageBag)
    {
        app()->logger()->info(
            "handled message",
            [
                'component' => "invokable_service_info",
                "message" => $messageBag->getProperties(),
                "body" => $messageBag->getBody()
            ]
        );
    }
}
