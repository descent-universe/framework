<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services;


use Descent\Contracts\ServiceProviderInterface;
use Descent\Services\Contracts\ServiceContainerInterface;
use Descent\Services\Contracts\ServiceExceptionInterface;
use Descent\Services\Contracts\ServiceInterface;
use Descent\Services\Entities\Factory;
use Descent\Services\Entities\Service;
use Descent\Services\Exceptions\ForgeException;
use Descent\Services\Exceptions\NotFoundException;

/**
 * Class Container
 * @package Descent\Services
 */
class Container implements ServiceContainerInterface
{
    /**
     * @var ServiceInterface[]
     */
    protected $interfaces = [];

    /**
     * @var object[]
     */
    protected $instances = [];

    /**
     * concrete ServiceInterface instance getter.
     *
     * @param string $interface
     * @throws ServiceExceptionInterface
     * @return ServiceInterface
     */
    public function get(string $interface): ServiceInterface
    {
        if ( ! $this->has($interface) ) {
            throw new NotFoundException(
                'Unknown interface: '.$interface
            );
        }

        return $this->interfaces[$this->marshalKey($interface)];
    }

    /**
     * checks whether the given interfaces are known to the container or not.
     *
     * @param \string[] ...$interface
     * @return bool
     */
    public function has(string ... $interface): bool
    {
        foreach ( $interface as $current ) {
            if ( ! array_key_exists($current, $this->interfaces) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * binds a given interface to a optionally provided concrete. If no concrete is provided, the provided interface
     * will be bound to itself.
     *
     * @param string $interface
     * @param string|object|null $concrete
     * @throws ServiceExceptionInterface when the provided concrete is not a string, object (not Closure) or null.
     * @return ServiceInterface
     */
    public function bind(string $interface, $concrete = null): ServiceInterface
    {
        $this->interfaces[$key = $this->marshalKey($interface)] = new Service($interface, $concrete);
        unset($this->interfaces[$key]);

        return $this->interfaces[$key];
    }

    /**
     * binds a given interface to a given callback as a factory. The callback must define the interface as its return
     * type.
     *
     * @param string $interface
     * @param callable $callback
     * @throws ServiceExceptionInterface when the provided callback does not have the provided interface as its return type.
     * @return ServiceInterface
     */
    public function factory(string $interface, callable $callback): ServiceInterface
    {
        $this->interfaces[$key = $this->marshalKey($interface)] = new Factory($interface, static::marshalFactory($callback));
        unset($this->interfaces[$key]);

        return $this->interfaces[$key];
    }

    /**
     * incubates the instance for a provided interface. Optional $parameters content supersedes assigned or incubated
     * parameters. Optionally enforces the provided optional parameter names.
     *
     * @param string $interface
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function make(string $interface, array $parameters = [], string ... $enforcedOptionalParameters)
    {
        if ( ! $this->has($interface) ) {
            return $this->forgeInstance($interface, $parameters, $enforcedOptionalParameters);
        }

        $service = $this->get($interface);

        foreach ( $service->getParameters() as $key => $value ) {
            if ( ! array_key_exists($key, $parameters) ) {
                $parameters[$key] = $value;
            }
        }

        if ( $service->isSingleton() && array_key_exists($service->getInterface(), $this->instances) ) {
            return $this->instances[$service->getInterface()];
        }

        if ( $service instanceof Service ) {
            $instance = $this->forgeInstance($service->getConcrete(), $parameters, $enforcedOptionalParameters);
        }
        else {
            $instance = $this->forgeCallable($service->getConcrete(), $parameters, $enforcedOptionalParameters);

            if ( ! is_object($instance) ) {
                throw new ForgeException(
                    'factory result is not an object'
                );
            }
        }

        if ( ! is_a($instance, $interface) ) {
            throw new ForgeException(
                'Resulting instance is not a subclass of '.$interface
            );
        }

        if ( $service->isSingleton() ) {
            $this->instances[$service->getInterface()] = $instance;
        }

        return $instance;
    }

    /**
     * calls the provided callback. Optional $parameters content supersedes incubated parameters. Optionally enforces
     * the provided optional parameter names.
     *
     * @param callable $callback
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function call(callable $callback, array $parameters = [], string ... $enforcedOptionalParameters)
    {
        return $this->forgeCallable(static::marshalFactory($callback), $parameters, $enforcedOptionalParameters);
    }

    /**
     * clones the instance. Optional provided interfaces define the copy limits of this container.
     *
     * @param \string[] ...$interfaces
     * @return ServiceContainerInterface
     */
    public function split(string ... $interfaces): ServiceContainerInterface
    {
        $newInstance = new static;

        if ( empty($interfaces) ) {
            $newInstance->interfaces = $this->interfaces;
            $newInstance->instances = $this->instances;

            return $newInstance;
        }

        foreach ( $interfaces as $current ) {
            $key = $this->marshalKey($current);

            if ( ! array_key_exists($key, $this->interfaces) ) {
                continue;
            }

            $newInstance->interfaces[$key] = $this->interfaces[$key];
            $newInstance->instances[$key] = $this->instances[$key];
        }

        return $newInstance;
    }

    /**
     * clones the instance. Optional provided interfaces are filtered from the new container instance.
     *
     * @param \string[] ...$interfaces
     * @return ServiceContainerInterface
     */
    public function expel(string ... $interfaces): ServiceContainerInterface
    {
        $newInstance = new static;

        if ( empty($interfaces) ) {
            $newInstance->interfaces = $this->interfaces;
            $newInstance->instances = $this->instances;

            return $newInstance;
        }

        foreach ( $interfaces as $current ) {
            $key = $this->marshalKey($current);

            if ( array_key_exists($key, $this->interfaces) ) {
                continue;
            }

            $newInstance->interfaces[$key] = $this->interfaces[$key];
            $newInstance->instances[$key] = $this->instances[$key];
        }

        return $newInstance;
    }

    /**
     * registers the provided service providers.
     *
     * @param ServiceProviderInterface[] ...$providers
     * @return ServiceContainerInterface
     */
    public function register(ServiceProviderInterface ... $providers): ServiceContainerInterface
    {
        foreach ( $providers as $current ) {
            $current->services($this);
        }

        return $this;
    }

    /**
     * marshals the interface key.
     *
     * @param string $interface
     * @return string
     */
    protected function marshalKey(string $interface): string
    {
        return strtolower(trim($interface, "\\"));
    }

    /**
     * forges a not registered interface.
     *
     * @param string $interface
     * @param mixed[] $parameters
     * @param string[] $optionalParams
     * @return object
     */
    protected function forgeInstance(string $interface, array $parameters, array $optionalParams)
    {
        $reflection = new \ReflectionClass($interface);

        if ( $reflection->isInterface() ) {
            throw new ForgeException(
                "interface [{$interface}] can not be instantiated"
            );
        }

        if ( ! $reflection->isInstantiable() ) {
            throw new ForgeException(
                "class [{$interface}] can not be instantiated"
            );
        }

        if ( ! $reflection->getConstructor() ) {
            return new $interface;
        }

        $dependencies = $this->forgeParameters(
            $interface,
            $parameters,
            $optionalParams,
            ... $reflection->getConstructor()->getParameters()
        );

        return $reflection->newInstance(... $dependencies);
    }

    /**
     * forges (executes) a callable.
     *
     * @param callable $callback
     * @param array $parameters
     * @param array $optionalParams
     * @return mixed
     */
    protected function forgeCallable(callable $callback, array $parameters, array $optionalParams)
    {
        $reflection = new \ReflectionFunction($callback);

        $dependencies = $this->forgeParameters(
            '~callback~',
            $parameters,
            $optionalParams,
            ... $reflection->getParameters()
        );

        return call_user_func_array($callback, $dependencies);
    }

    /**
     * forges a stack of reflection parameters into dependencies
     *
     * @param string $interface
     * @param array $parameters
     * @param array $optionalParams
     * @param \ReflectionParameter[] ...$reflections
     * @throws ForgeException
     * @return array
     */
    protected function forgeParameters(string $interface, array $parameters, array $optionalParams, \ReflectionParameter ... $reflections)
    {
        $dependencies = [];

        foreach ( $reflections as $parameter ) {
            if ( array_key_exists($parameter->getPosition(), $parameters) ) {
                $dependencies[$parameter->getPosition()] = $parameters[$parameter->getPosition()];
                continue;
            }

            if ( array_key_exists($parameter->getName(), $parameters) ) {
                $dependencies[$parameter->getPosition()] = $parameters[$parameter->getName()];
                continue;
            }

            if ( ! $parameter->isOptional() && $parameter->getClass() && $this->has($class = $parameter->getClass()->getName()) ) {
                $dependencies[$parameter->getPosition()] = $this->make($class);
                continue;
            }

            if ( ! $parameter->isOptional() && $parameter->getClass() && ! $this->has($class = $parameter->getClass()->getName()) ) {
                $dependencies[$parameter->getPosition()] = $this->forgeInstance($class, [], []);
                continue;
            }

            if ( $parameter->isOptional() && $parameter->getClass() && array_key_exists($class = $parameter->getClass()->getName(), $optionalParams) ) {
                if ( $this->has($class) ) {
                    $dependencies[$parameter->getPosition()] = $this->make($class);
                }
                else {
                    $dependencies[$parameter->getPosition()] = $this->forgeInstance($class, [], []);
                }
                continue;
            }

            if ( ! $parameter->isDefaultValueAvailable() ) {
                throw new ForgeException(
                    "class [{$interface}] can not be instantiated due to dependencies"
                );
            }

            $dependencies = $parameter->getDefaultValue();
        }

        return $dependencies;
    }

    /**
     * marshals a factory closure from the provided callback. Polyfills \Closure::fromCallable of PHP 7.1+.
     *
     * @param callable $callback
     * @return \Closure
     */
    public static function marshalFactory(callable $callback): \Closure
    {
        if ( version_compare(PHP_VERSION, '7.1.0', '>=') ) {
            return \Closure::fromCallable($callback);
        }

        if ( is_array($callback) ) {
            list($concrete, $method) = $callback;
            return (new \ReflectionMethod($concrete, $method))->getClosure($concrete);
        }

        if ( is_string($callback) ) {
            return (new \ReflectionFunction($callback))->getClosure();
        }

        if ( is_object($callback) && ! $callback instanceof \Closure ) {
            return (new \ReflectionClass($callback))->getMethod('__invoke')->getClosure($callback);
        }

        return $callback;
    }
}