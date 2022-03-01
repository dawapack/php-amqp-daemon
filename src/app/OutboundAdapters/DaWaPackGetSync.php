<?php

declare(strict_types=1);

namespace DaWaPack\OutboundAdapters;

use Chassis\Framework\OutboundAdapters\OutboundAbstractAdapter;

class DaWaPackGetSync extends OutboundAbstractAdapter
{
    /**
     * Set this property in order to create a sync over async call type
     *
     * @var boolean
     */
    protected bool $isSyncOverAsync = true;

    /**
     * Sync over async must provide routing key (the queue will be hit by the request)
     *
     * @var string
     */
    protected string $routingKey = "DaWaPack.Q.Commands";

    /**
     * To be more specific, we can set the message type here he will be filled by the
     * outbound adapter. The other way is to set up the message type property.
     *
     * @var string
     */
    protected string $operation = "getSomething";
}
