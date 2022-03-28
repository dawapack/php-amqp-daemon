<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Logger\Logger;
use Chassis\Framework\Services\AbstractService;
use DaWaPack\Message\ApplicationMessage;

/**
 * use /tests/phpstorm-helpers/amqp.http to trigger this sample
 */
class DemoService extends AbstractService
{
    /**
     * This is an example of active RPC request handler
     *
     * @operation getSomething
     */
    public function get()
    {
        Logger::info(
            "handle request - DemoService::get()",
            [
                "component" => "application_info",
                "message" => [
                    'properties' => $this->message->getProperties(),
                    'headers' => $this->message->getHeaders(),
                    'body' => $this->message->getBody(),
                ]
            ]
        );

        return $this->response(
            new ApplicationMessage(
                ["my" => "body_response"], ["my" => "header_response"]
            ),
            200,
            "DONE"
        );
    }

    /**
     * @operation createSomething
     */
    public function create()
    {
        Logger::info(
            "handle message - DemoService::create()",
            [
                "component" => "application_info",
                "message" => [
                    'properties' => $this->message->getProperties(),
                    'headers' => $this->message->getHeaders(),
                    'body' => $this->message->getBody(),
                ]
            ]
        );

        // Active RPC sample - call get action, wait 5 seconds for response
        $response = $this->send(
            "getSomethingSync",
            new ApplicationMessage(["my" => "get_something_sync_body"])
        );
        if (!is_null($response)) {
            Logger::info(
                "got response to sync over async call - DemoService::create()",
                [
                    "component" => "application_info",
                    "message" => [
                        'properties' => $response->getProperties(),
                        'headers' => $response->getHeaders(),
                        'body' => $response->getBody(),
                    ]
                ]
            );
        }

        // Passive RPC sample - call get action fire and forget.
        $this->send(
            "getSomethingAsync",
            new ApplicationMessage(["my" => "get_something_async_body"])
        );

        // Fire and forget - just push a message
        $this->send(
            "deleteSomething",
            new ApplicationMessage(["my" => "delete_something_fire_and_forget"])
        );
    }

    /**
     * @operation deleteSomething
     */
    public function delete()
    {
        Logger::info(
            "got request fire and forget - DemoService::delete()",
            [
                "component" => "application_info",
                "message" => [
                    'properties' => $this->message->getProperties(),
                    'headers' => $this->message->getHeaders(),
                    'body' => $this->message->getBody(),
                ]
            ]
        );

        // Event sample - just push a message
        $this->send(
            "deleteSomethingEvent",
            new ApplicationMessage(["my" => "delete_something_event"])
        );
    }
}
