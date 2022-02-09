<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Providers;

use DaWaPack\Classes\Threads\ThreadInstanceInterface;
use DaWaPack\Providers\ThreadInstanceServiceProvider;
use DaWaPack\Tests\AppTestCase;

class ThreadInstanceServiceProviderTest extends AppTestCase
{
    /**
     * @return void
     */
    public function testApplicationCanInstantiateThreadInstanceServiceProviderClass(): void
    {
        $this->app->addServiceProvider(new ThreadInstanceServiceProvider());
        $this->assertInstanceOf(
            ThreadInstanceInterface::class,
            $this->app->get(ThreadInstanceInterface::class)
        );
    }
}