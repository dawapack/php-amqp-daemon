<?php
declare(strict_types=1);

namespace DaWaPack\Providers;

use Chassis\Framework\Providers\OutboundRoutingServiceProvider;
use DaWaPack\OutboundAdapters\DaWaPackDelete;
use DaWaPack\OutboundAdapters\DaWaPackGetSync;
use DaWaPack\OutboundAdapters\DaWaPackGetAsync;
use DaWaPack\OutboundAdapters\DaWaPackDeletedEvents;

class OutboundServiceProvider extends OutboundRoutingServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $routes = [
        // Active & passive RPC
        'getSomethingSync' => DaWaPackGetSync::class,
        'getSomethingAsync' => DaWaPackGetAsync::class,

        // Fire and forget
        'deleteSomething' => DaWaPackDelete::class,

        // Events
        'deleteSomethingEvent' => DaWaPackDeletedEvents::class,
    ];
}