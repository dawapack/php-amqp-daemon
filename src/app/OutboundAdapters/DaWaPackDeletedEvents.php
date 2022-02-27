<?php

declare(strict_types=1);

namespace DaWaPack\OutboundAdapters;

use Chassis\Framework\OutboundAdapters\OutboundAbstractAdapter;

class DaWaPackDeletedEvents extends OutboundAbstractAdapter
{
    /**
     * This will use a dedicated channel for events calls
     *
     * @var string
     */
    protected string $channelName = "outbound/events";

    /**
     * Events must provide the routing key - see exchange bindings of the channel
     *
     * @var string
     */
    protected string $routingKey = "DaWaPack.RK.EventLoopback";

    /**
     * To be more specific, we can set the message type here, and will be filled by the
     * outbound adapter. The other way is to set up the message type property and remove
     * this property.
     *
     * @var string
     */
    protected string $operation = "somethingDeleted";
}
