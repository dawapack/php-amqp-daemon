<?php

declare(strict_types=1);

namespace DaWaPack\Providers;

use Chassis\Framework\Providers\RoutingServiceProvider;
use DaWaPack\OutboundAdapters\DemoOperationDelete;
use DaWaPack\OutboundAdapters\DemoOperationDeletedEvents;
use DaWaPack\OutboundAdapters\DemoOperationGetAsync;
use DaWaPack\OutboundAdapters\DemoOperationGetSync;
use DaWaPack\Services\DemoDeleteEventService;
use DaWaPack\Services\DemoMoreService;
use DaWaPack\Services\DemoService;

class MessageRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $inboundRoutes = [
        'createSomething' => [DemoService::class, 'create'],
        'getSomething' => [DemoService::class, 'get'],
        'getSomethingResponse' => [DemoMoreService::class, 'complete'],
        'deleteSomething' => [DemoService::class, 'delete'],
        'somethingDeleted' => DemoDeleteEventService::class,
    ];

    /**
     * @var array|string[]
     */
    protected array $outboundRoutes = [
        'getSomethingSync' => DemoOperationGetSync::class,
        'getSomethingAsync' => DemoOperationGetAsync::class,
        'deleteSomething' => DemoOperationDelete::class,
        'deleteSomethingEvent' => DemoOperationDeletedEvents::class,
    ];
}
