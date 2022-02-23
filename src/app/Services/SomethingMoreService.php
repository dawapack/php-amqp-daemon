<?php

declare(strict_types=1);

namespace DaWaPack\Services;

use Chassis\Framework\Services\BrokerAbstractService;

class SomethingMoreService extends BrokerAbstractService
{
    /**
     * @operation getSomething
     */
    public function complete()
    {
//        $jobId = $this->message->getProperty("application_headers")["jobId"];
//        var_dump("[$jobId] " . __METHOD__);

        // dump the response
//        $this->app->logger()->debug(
//            "method complete of something more service triggered",
//            [
//                "component" => self::LOGGER_COMPONENT_PREFIX . "complete",
//                "service" => __METHOD__,
//                "operation" => $this->message->getProperty("type"),
//                "payload" => $this->message->getBody(),
//                "status" => [
//                    "code" => $this->message->getProperty("application_headers")["statusCode"],
//                    "message" => $this->message->getProperty("application_headers")["statusMessage"]
//                ]
//            ]
//        );
    }
}
