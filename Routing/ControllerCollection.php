<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Routing;


use Descent\Routing\Exceptions\IncompatibleBindingException;
use Descent\Routing\Exceptions\NoBoundControllerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class ControllerCollection
 *
 * @package Descent\Routing
 *
 * @method ControllerCollection assert(string $variable, string $regexp)
 * @method ControllerCollection value(string $variable, mixed $default)
 * @method ControllerCollection convert(string $variable, callable $callback)
 * @method ControllerCollection method(string $method)
 * @method ControllerCollection requireHttp()
 * @method ControllerCollection requireHttps()
 * @method ControllerCollection before(callable $callback)
 * @method ControllerCollection after(callable $callback)
 * @method ControllerCollection when(string $condition)
 */
class ControllerCollection
{
    /**
     * @var RouteEntity[]
     */
    protected $controllers = [];

    /**
     * @var Route
     */
    protected $defaultRoute;

    /**
     * @var \Closure
     */
    protected $defaultController;

    /**
     * @var
     */
    protected $prefix;

    /**
     * @var RouteCollection
     */
    protected $routesFactory;

    /**
     * @var RoutingFactoriesAdapter
     */
    protected $routingFactories;

    /**
     * ControllerCollection constructor.
     * 
     * @param Route $defaultRoute
     * @param RouteCollection $routesFactory
     * @param RoutingFactoriesAdapter $routingFactories
     */
    final public function __construct(
        Route $defaultRoute,
        RouteCollection $routesFactory,
        RoutingFactoriesAdapter $routingFactories
    ) {
        $this->defaultRoute = $defaultRoute;
        $this->routesFactory = $routesFactory;
        $this->routingFactories = $routingFactories;

        $this->defaultController = function(Request $request) {
            throw new NoBoundControllerException(
                sprintf(
                    'The "%s" route must have a controller to run when it matches',
                    $request->attributes->get('_route')
                )
            );
        };
    }

    /**
     * Mounts controllers under the given route prefix.
     *
     * @param string $prefix
     * @param ControllerCollection|callable $controllers
     */
    final public function mount(string $prefix, $controllers)
    {
        if ( is_callable($controllers) ) {
            $collection = $this->routingFactories->createControllerCollection(
                $this->routingFactories->createRoute(),
                $this->routingFactories->createRouteCollection()
            );

            $controllers($collection);
        }
        elseif ( ! $controllers instanceof self ) {
            throw new IncompatibleBindingException(
                'The "mount" method takes either a "ControllerCollection" instance or callable'
            );
        }

        $controllers->prefix = $prefix;

        $this->controllers[] = $controllers;
    }

    /**
     * Maps a pattern to a callable
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function match(string $pattern, callable $to = null): RouteEntity
    {
        $route = clone $this->defaultRoute;
        $route->setPath($pattern);
        $this->controllers[] = $controller = $this->routingFactories->createRouteEntity($route);
        $route->setDefault('_controller', $to ?? $this->defaultController);

        return $controller;
    }

    /**
     * Maps a GET request to a callable
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function get(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('GET');
    }

    /**
     * Maps a POST request to a callable.
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function post(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('POST');
    }

    /**
     * Maps a PUT request to a callable.
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function put(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('PUT');
    }

    /**
     * Maps a DELETE request to a callable.
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function delete(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('DELETE');
    }

    /**
     * Maps a OPTIONS request to a callable.
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function options(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('OPTIONS');
    }

    /**
     * Maps a PATCH request to a callable.
     *
     * @param string $pattern
     * @param callable|null $to
     * @return RouteEntity
     */
    final public function patch(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('PATCH');
    }

    /**
     * Persists and freezes staged controllers.
     *
     * @return RouteCollection
     */
    final public function flush(): RouteCollection
    {
        return $this->doFlush('', $this->routesFactory);
    }

    /**
     * recursion aware flash execution.
     *
     * @param string $prefix
     * @param RouteCollection $routes
     * @return RouteCollection
     */
    private function doFlush(string $prefix, RouteCollection $routes): RouteCollection
    {
        if ( ! empty($prefix) ) {
            $prefix = '/'.trim(trim($prefix), '/');
        }

        foreach ( $this->controllers as $controller ) {
            if ( $controller instanceof Controller ) {
                $controller->getRoute()->setPath($prefix.$controller->getRoute()->getPath());

                if ( ! $name = $controller->getRouteName() ) {
                    $name = $base = $controller->generateRouteName('');
                    $i = 0;
                    while ( $routes->get($name) ) {
                        $name = $base.'_'.++$i;
                    }

                    $controller->bind($name);
                }

                $routes->add($name, $controller->getRoute());
                $controller->freeze();
            }
            elseif ( $controller instanceof self ) {
                $controller->doFlush($prefix.$controller->prefix, $routes);
            }
        }

        $this->controllers = [];

        return $routes;
    }

    /**
     * Magic caller for route methods applied to the default route and all current routes.
     *
     * @param string $method
     * @param array $arguments
     * @return ControllerCollection
     */
    final public function __call(string $method, array $arguments = []): ControllerCollection
    {
        if ( ! method_exists($this->defaultRoute, $method) ) {
            throw new \BadMethodCallException(
                sprintf(
                    'Method "%s::%s" does not exists',
                    get_class($this->defaultRoute),
                    $method
                )
            );
        }

        call_user_func_array([$this->defaultRoute, $method], $arguments);

        foreach ( $this->controllers as $controller ) {
            call_user_func_array([$controller, $method], $arguments);
        }

        return $this;
    }
}