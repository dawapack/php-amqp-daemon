<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Exceptions\BrokerConfigurationException;
use Spatie\DataTransferObject\DataTransferObject;

class ConnectionConfiguration extends DataTransferObject implements ConfigurationInterface
{

    /**
     *
     * @var string
     */
    public string $protocol;

    /**
     * @var string
     */
    public string $host;

    /**
     * @var int
     */
    public int $port;

    /**
     * @var string
     */
    public string $user;

    /**
     * @var string
     */
    public string $pass;

    /**
     * @var string
     */
    public string $vhost;

    /**
     * @var bool
     */
    public bool $insist = false;

    /**
     * @var string
     */
    public string $login_method = 'AMQPLAIN';

    /**
     * @var string|null
     * @deprecated
     */
    public ?string $login_response = null;

    /**
     * @var string
     */
    public string $locale = 'en_US';

    /**
     * @var float
     */
    public float $connection_timeout = 3.0;

    /**
     * @var float
     */
    public float $read_write_timeout = 3.0;

    /**
     * @var resource|array|null
     */
    public $context = null;

    /**
     * @var bool
     */
    public bool $keepalive = false;

    /**
     * @var int
     */
    public int $heartbeat = 0;

    /**
     * @var float
     */
    public float $channel_rpc_timeout = 0.0;

    /**
     * @var string|null
     */
    public ?string $ssl_protocol = null;

    /**
     * ConnectionConfiguration constructor.
     *
     * @param ConfigurationLoaderInterface $configurationLoader
     *
     * @throws BrokerConfigurationException
     */
    public function __construct(
        ConfigurationLoaderInterface $configurationLoader
    ) {
        parent::__construct($configurationLoader->loadConfig("broker.connection"));
    }


    /**
     * @inheritDoc
     */
    public function toFunctionArguments(bool $onlyValues = true): array
    {
        return $onlyValues
            ? array_values($this->except("protocol")->toArray())
            : $this->except("protocol")->toArray();
    }
}
