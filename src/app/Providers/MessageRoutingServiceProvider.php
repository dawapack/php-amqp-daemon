<?php

declare(strict_types=1);

namespace DaWaPack\Providers;

use Chassis\Framework\Providers\RoutingServiceProvider;
use DaWaPack\OutboundAdapters\DaWaPackDelete;
use DaWaPack\OutboundAdapters\DaWaPackDeletedEvents;
use DaWaPack\OutboundAdapters\DaWaPackGetAsync;
use DaWaPack\OutboundAdapters\DaWaPackGetSync;
use DaWaPack\Services\DeleteEventService;
use DaWaPack\Services\SomethingMoreService;
use DaWaPack\Services\SomethingService;

class MessageRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $inboundRoutes = [
        'createSomething' => [SomethingService::class, 'create'],
        'getSomething' => [SomethingService::class, 'get'],
        'getSomethingResponse' => [SomethingMoreService::class, 'complete'],
        'deleteSomething' => [SomethingService::class, 'delete'],
        'somethingDeleted' => DeleteEventService::class,
    ];

    /**
     * @var array|string[]
     */
    protected array $outboundRoutes = [
        'getSomethingSync' => DaWaPackGetSync::class,
        'getSomethingAsync' => DaWaPackGetAsync::class,
        'deleteSomething' => DaWaPackDelete::class,
        'deleteSomethingEvent' => DaWaPackDeletedEvents::class,
    ];
}
