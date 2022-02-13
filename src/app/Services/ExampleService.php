<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Brokers\Amqp\BrokerResponse;
use Chassis\Framework\Services\BrokerAbstractService;

class ExampleService extends BrokerAbstractService
{
    /**
     * @return BrokerResponse|void
     */
    public function get()
    {
        $this->app->logger()->info(
            "handled message",
            [
                'component' => self::LOGGER_COMPONENT_PREFIX . "info",
                "message" => $this->messageBag->getProperties(),
                "body" => $this->messageBag->getBody()
            ]
        );
    }
}
