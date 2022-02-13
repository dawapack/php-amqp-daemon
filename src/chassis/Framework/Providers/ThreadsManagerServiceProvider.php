<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Providers;

use DaWaPack\Chassis\Framework\Threads\Configuration\ThreadsConfigurationInterface;
use DaWaPack\Chassis\Framework\Threads\ThreadsManager;
use DaWaPack\Chassis\Framework\Threads\ThreadsManagerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use parallel\Events;
use Psr\Log\LoggerInterface;

class ThreadsManagerServiceProvider extends AbstractServiceProvider
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function provides(string $id): bool
    {
        return $id === ThreadsManagerInterface::class;
    }

    /**
     * @return void
     */
    public function register(): void
    {
        // Instantiate ThreadsManager
        $this->getContainer()
            ->add(ThreadsManagerInterface::class, ThreadsManager::class)
            ->addArguments([
                ThreadsConfigurationInterface::class,
                new Events(),
                LoggerInterface::class
            ]);
    }
}
