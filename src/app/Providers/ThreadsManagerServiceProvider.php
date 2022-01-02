<?php
declare(strict_types=1);

namespace DaWaPack\Providers;

use DaWaPack\Classes\Threads\ThreadsManager;
use DaWaPack\Interfaces\ThreadsConfigurationInterface;
use DaWaPack\Interfaces\ThreadsManagerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use parallel\Events;

class ThreadsManagerServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return $id === ThreadsManagerInterface::class;
    }

    public function register(): void
    {
        // Instantiate ThreadsManager
        $this->getContainer()
            ->add(ThreadsManagerInterface::class, ThreadsManager::class)
            ->addArguments([ThreadsConfigurationInterface::class, new Events()]);
    }
}
