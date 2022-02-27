<?php

namespace DaWaPack\Providers;

use Chassis\Framework\Providers\InboundRoutingServiceProvider;
use DaWaPack\Services\DeleteEventService;
use DaWaPack\Services\SomethingMoreService;
use DaWaPack\Services\SomethingService;

class InboundServiceProvider extends InboundRoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $routes = [
        // main entry point
        'createSomething' => [SomethingService::class, 'create'],

        // handle getSomething operation - active & passive RPC
        'getSomething' => [SomethingService::class, 'get'],

        // handle response of getSomething - passive RPC mode
        'getSomethingResponse' => [SomethingMoreService::class, 'complete'],

        // handle deleteSomething - fire and forget
        'deleteSomething' => [SomethingService::class, 'delete'],

        // handle deleted events
        'somethingDeleted' => DeleteEventService::class,
    ];
}
