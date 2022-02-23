<?php

namespace DaWaPack\Providers;

use Chassis\Framework\Providers\RoutingServiceProvider;
use DaWaPack\Services\ClearTrackerService;
use DaWaPack\Services\SomethingMoreService;
use DaWaPack\Services\SomethingService;
use DaWaPack\Services\PushTrackerService;

class RequestRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $routes = [
        'createSomething' => [SomethingService::class, 'create'],
        'getSomething' => [SomethingService::class, 'get'],
        'updateSomething' => [SomethingService::class, 'update'],
        'deleteSomething' => [SomethingService::class, 'delete'],
        'getSomethingResponse' => [SomethingMoreService::class, 'complete'],
        'pushTracker' => PushTrackerService::class,
        'clearTracker' => ClearTrackerService::class,
    ];
}
