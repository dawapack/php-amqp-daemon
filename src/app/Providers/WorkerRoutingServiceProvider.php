<?php

namespace DaWaPack\Providers;

use DaWaPack\Chassis\Framework\Providers\RoutingServiceProvider;
use DaWaPack\Services\ExampleService;
use DaWaPack\Services\InvokableExampleService;

class WorkerRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $routes = [
        'getExample' => [ExampleService::class, 'get'],
        'getInvokableExample' => InvokableExampleService::class
    ];
}
