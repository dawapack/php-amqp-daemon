<?php

declare(strict_types=1);

namespace DaWaPack\OutboundAdapters;

use Chassis\Framework\OutboundAdapters\OutboundAbstractAdapter;

class DaWaPackDelete extends OutboundAbstractAdapter
{
    /**
     * This will use a dedicated channel for events calls
     *
     * @var string
     */
    protected string $channelName = "outbound/commands";

    /**
     * Fire and forget must provide the routing key - see exchange bindings of the channel
     *
     * @var string
     */
    protected string $routingKey = "DaWaPack.RK.CommandFireAndForget";

    /**
     * To be more specific, we can set the message type here, he will be filled by the
     * outbound adapter. The other way is to set up the message type property.
     *
     * @var string
     */
    protected string $operation = "deleteSomething";
}
