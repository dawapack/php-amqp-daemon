<?php

declare(strict_types=1);

namespace DaWaPack\OutboundAdapters;

use Chassis\Framework\Adapters\Operations\AbstractOperationsAdapter;

class DemoOperationGetAsync extends AbstractOperationsAdapter
{
    /**
     * Use a dedicated channel - passive RPC calls type
     *
     * @var string
     */
    protected string $channelName = "rpc/outbound/commands";

    /**
     * Passive RPC must provide the routing key - see exchange bindings of the channel
     *
     * @var string
     */
    protected string $routingKey = "DaWaPack.RK.CommandLoopback";

    /**
     * Passive RPC must provide the reply to queue
     *
     * @var string
     */
    protected string $replyTo = "DaWaPack.Q.Responses";

    /**
     * To be more specific, we can set the message type here and will be filled by the
     * outbound adapter. The other way is to set up the message type property.
     *
     * @var string
     */
    protected string $operation = "getSomething";
}
