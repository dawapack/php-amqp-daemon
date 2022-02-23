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
     * @operation getSomething
     */
    public function get()
    {
        $jobId = $this->message->getProperty("application_headers")["jobId"];
//        var_dump("[$jobId] " . __METHOD__);

        // SAMPLE - handle RPC request, return results
        return $this->response([
            "code" => 200,
            "message" => "DONE",
            "data" => [
                "items" => [
                    "jobId" => $jobId,
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
//        var_dump(__METHOD__);

        // SAMPLE - call get action - active RPC - very poor performance
        $response = ($this->request("getSomething", []))
            ->setRoutingKey("DaWaPack.Q.Commands")
            ->call(5);

//        if (!is_null($response)) {
//            var_dump($response->getBody());
//        }

        // SAMPLE - call get action - passive RPC
        ($this->request("getSomething", []))
            ->setRoutingKey("DaWaPack.Q.Commands")
            ->setReplyTo("DaWaPack.Q.Responses")
            ->push();

        // SAMPLE - push message to update action - Fire And Forget
        ($this->request("updateSomething", []))
            ->setRoutingKey("DaWaPack.Q.Commands")
            ->push();

        // SAMPLE - push message to delete action - Fire And Forget
        ($this->request("deleteSomething", []))
            ->setRoutingKey("DaWaPack.Q.Commands")
            ->push();
        $end = microtime(true);

        var_dump("elapsed time - " . ($end - $start));
    }

    /**
     * @operation updateSomething
     */
    public function update()
    {
//        $jobId = $this->message->getProperty("application_headers")["jobId"];
//        var_dump("[$jobId] " . __METHOD__);

        // SAMPLE - push message to PushTracker service - Fire And Forget
        ($this->request("pushTracker", []))
            ->setRoutingKey("DaWaPack.Q.Commands")
            ->push();
    }

    /**
     * @operation deleteSomething
     */
    public function delete()
    {
//        $jobId = $this->message->getProperty("application_headers")["jobId"];
//        var_dump("[$jobId] " . __METHOD__);

        // SAMPLE - push message to ClearTracker service - Fire And Forget
        ($this->request("clearTracker", []))
            ->setRoutingKey("DaWaPack.Q.Commands")
            ->push();
    }
}
