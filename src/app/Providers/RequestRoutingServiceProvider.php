<?php

namespace DaWaPack\Providers;

use Chassis\Framework\Providers\RoutingServiceProvider;
use DaWaPack\Services\ExampleService;
use DaWaPack\Services\InvokableExampleService;

class RequestRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $routes = [
        'getExample' => [ExampleService::class, 'get'],
        'getInvokableExample' => InvokableExampleService::class
    ];
}
