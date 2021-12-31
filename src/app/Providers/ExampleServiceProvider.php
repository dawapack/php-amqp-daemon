<?php

namespace DaWaPack\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Psr\Log\LoggerInterface;

class ExampleServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    /**
     * In much the same way, this method has access to the container
     * itself and can interact with it however you wish, the difference
     * is that the boot method is invoked as soon as you register
     * the service provider with the container meaning that everything
     * in this method is eagerly loaded.
     *
     * If you wish to apply inflectors or register further service providers
     * from this one, it must be from a bootable service provider like
     * this one, otherwise they will be ignored.
     *
     * @return void
     */
    public function boot(): void
    {
        // Sample inflector
        $this->getContainer()
            ->inflector(LoggerInterface::class) // can be a concrete also
            ->invokeMethod('someMethod', ['firstArgMethod', 'secondArgMethod']);
    }

    /**
     * The provides method is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @param string $id
     *
     * @return bool
     */
    public function provides(string $id): bool
    {
        $services = [
            // key/value pair
            'key',
            // Interface
            Some\ControllerInterface::class,
            // Concretes
            Some\ModelCustom::class,
            Some\RequestCustom::class,
        ];

        return in_array($id, $services);
    }

    /**
     * The register method is where you define services
     * in the same way you would directly with the container.
     * A convenience getter for the container is provided, you
     * can invoke any of the methods you would when defining
     * services directly, but remember, any alias added to the
     * container here, when passed to the `provides` nethod
     * must return true, or it will be ignored by the container.
     *
     * @return void
     */
    public function register(): void
    {
        // add key/value pair
        $this->getContainer()->add('key', 'value');
        // add by interface
        $this->getContainer()
            ->add(Some\ControllerInterface::class, Some\ControllerConcrete::class)
            ->addArgument(Some\RequestCustom::class)    // first constructor argument
            ->addArgument(Some\ModelCustom::class)      // second constructor argument
        ;
        // add by concretes
        $this->getContainer()->add(Some\RequestCustom::class);
        $this->getContainer()->add(Some\ModelCustom::class);
    }
}
