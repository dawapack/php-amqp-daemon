<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Services\BrokerAbstractService;

class SomethingMoreService extends BrokerAbstractService
{
    /**
     * This is an example of passive RPC response handler
     *
     * @operation getSomethingResponse
     */
    public function complete()
    {
        $this->app->logger()->debug(
            "method complete of something more service triggered",
            [
                "component" => self::LOGGER_COMPONENT_PREFIX . "complete",
                "service" => __METHOD__,
                "operation" => $this->message->getProperty("type"),
                "payload" => $this->message->getBody(),
                "status" => [
                    "code" => $this->message->getProperty("application_headers")["statusCode"],
                    "message" => $this->message->getProperty("application_headers")["statusMessage"]
                ]
            ]
        );
    }
}
