<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Contracts;


use Descent\Contracts\ServiceProviderInterface;

interface ServiceContainerInterface
{
    /**
     * concrete ServiceInterface instance getter.
     *
     * @param string $interface
     * @throws ServiceExceptionInterface
     * @return ServiceInterface
     */
    public function get(string $interface): ServiceInterface;

    /**
     * checks whether the given interfaces are known to the container or not.
     *
     * @param \string[] ...$interface
     * @return bool
     */
    public function has(string ... $interface): bool;

    /**
     * binds a given interface to a optionally provided concrete. If no concrete is provided, the provided interface
     * will be bound to itself.
     *
     * @param string $interface
     * @param string|object|null $concrete
     * @throws ServiceExceptionInterface when the provided concrete is not a string, object (not Closure) or null.
     * @return ServiceInterface
     */
    public function bind(string $interface, $concrete = null): ServiceInterface;

    /**
     * binds a given interface to a given callback as a factory. The callback must define the interface as its return
     * type.
     *
     * @param string $interface
     * @param callable $callback
     * @throws ServiceExceptionInterface when the provided callback does not have the provided interface as its return type.
     * @return ServiceInterface
     */
    public function factory(string $interface, callable $callback): ServiceInterface;

    /**
     * incubates the instance for a provided interface. Optional $parameters content supersedes assigned or incubated
     * parameters. Optionally enforces the provided optional parameter names.
     *
     * @param string $interface
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function make(string $interface, array $parameters = [], string ... $enforcedOptionalParameters);

    /**
     * calls the provided callback. Optional $parameters content supersedes incubated parameters. Optionally enforces
     * the provided optional parameter names.
     *
     * @param callable $callback
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function call(callable $callback, array $parameters = [], string ... $enforcedOptionalParameters);

    /**
     * clones the instance. Optional provided interfaces define the copy limits of this container.
     *
     * @param \string[] ...$interfaces
     * @return ServiceContainerInterface
     */
    public function split(string ... $interfaces): ServiceContainerInterface;

    /**
     * clones the instance. Optional provided interfaces are filtered from the new container instance.
     *
     * @param \string[] ...$interfaces
     * @return ServiceContainerInterface
     */
    public function expel(string ... $interfaces): ServiceContainerInterface;

    /**
     * registers the provided service providers.
     *
     * @param ServiceProviderInterface[] ...$providers
     * @return ServiceContainerInterface
     */
    public function register(ServiceProviderInterface ... $providers): ServiceContainerInterface;
}