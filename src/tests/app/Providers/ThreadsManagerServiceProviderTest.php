<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Providers;

use DaWaPack\Classes\Threads\Configuration\ThreadsConfiguration;
use DaWaPack\Classes\Threads\Configuration\ThreadsConfigurationInterface;
use DaWaPack\Classes\Threads\ThreadsManagerInterface;
use DaWaPack\Providers\ThreadsManagerServiceProvider;
use DaWaPack\Tests\AppTestCase;

class ThreadsManagerServiceProviderTest extends AppTestCase
{
    /**
     * @return void
     */
    public function testApplicationCanInstantiateThreadsManagerServiceProviderClass(): void
    {
        $this->app->addServiceProvider(new ThreadsManagerServiceProvider());
        $this->app->add(ThreadsConfigurationInterface::class, ThreadsConfiguration::class)
            ->addArgument($this->app->get("config")->get("threads"));
        $this->assertInstanceOf(
            ThreadsManagerInterface::class,
            $this->app->get(ThreadsManagerInterface::class)
        );

    }
}