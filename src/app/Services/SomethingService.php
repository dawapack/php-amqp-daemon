<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Services\BrokerAbstractService;

/**
 * use /tests/phpstorm-helpers/amqp.http to trigger this sample
 */
class SomethingService extends BrokerAbstractService
{
    /**
     * This is an example of active RPC request handler
     *
     * @operation getSomething
     */
    public function get()
    {
        return $this->response([
            "code" => 200,
            "message" => "DONE",
            "data" => [
                "items" => [
                    "jobId" => $this->message->getProperty("application_headers")["jobId"],
                    "correlation_id" => $this->message->getProperty("correlation_id"),
                    "message_id" => $this->message->getProperty("message_id")
                ],
                "meta" => null
            ]
        ])->setStatus(200, "DONE");
    }

    /**
     * @operation createSomething
     */
    public function create()
    {
        $start = microtime(true);

        // Active RPC sample - call get action, wait 5 seconds for response
        $response = $this->send("getSomethingSync", $this->request([]));
        if (!is_null($response)) {
            // TODO: execute your business logic based on response
        }

        // Passive RPC sample - call get action fire and forget.
        $this->send("getSomethingAsync", $this->request([]));

        // Fire and forget - just push a message
        $this->send("deleteSomething", $this->request([]));

        var_dump("elapsed time - " . (microtime(true) - $start));
    }

    /**
     * @operation deleteSomething
     */
    public function delete()
    {
        // Event sample - just push a message
        $this->send("deleteSomethingEvent", $this->request([]));
    }
}
