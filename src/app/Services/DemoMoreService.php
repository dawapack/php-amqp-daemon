<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Logger\Logger;
use Chassis\Framework\Services\AbstractService;

class DemoMoreService extends AbstractService
{
    /**
     * This is an example of passive RPC response handler
     *
     * @operation getSomethingResponse
     */
    public function complete()
    {
        Logger::info(
            "got response to async call - DemoMoreService::complete()",
            [
                "component" => "application_info",
                "message" => [
                    'properties' => $this->message->getProperties(),
                    'headers' => $this->message->getHeaders(),
                    'body' => $this->message->getBody(),
                ]
            ]
        );
    }
}
