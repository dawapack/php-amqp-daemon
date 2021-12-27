<?php
declare(strict_types=1);

namespace DaWaPack\Classes;

use DaWaPack\Interfaces\ThreadsConfigurationInterface;
use DaWaPack\Interfaces\ThreadsManagerInterface;

class ThreadsManager implements ThreadsManagerInterface
{

    /**
     * @var ThreadsConfigurationInterface
     */
    private ThreadsConfigurationInterface $threadConfiguration;

    /**
     * ThreadsManager constructor.
     *
     * @param ThreadsConfigurationInterface $threadConfiguration
     */
    public function __construct(
        ThreadsConfigurationInterface $threadConfiguration
    ) {
        $this->threadConfiguration = $threadConfiguration;
    }
}
