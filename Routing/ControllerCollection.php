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

class ControllerCollection
{
    /**
     * @var RouteEntity[]
     */
    protected $controllers = [];
    protected $defaultRoute;
    protected $defaultController;
    protected $prefix;
    protected $routesFactory;
    protected $controllersFactory;

    public function __construct(Route $defaultRoute, RouteCollection $routesFactory = null, callable $controllersFactory = null)
    {
        $this->defaultRoute = $defaultRoute;
        $this->routesFactory = $routesFactory;
        $this->controllersFactory = $controllersFactory;

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
    public function mount(string $prefix, $controllers)
    {
        if ( is_callable($controllers) ) {
            $collection = is_callable($this->controllersFactory)
                ? call_user_func($this->controllersFactory)
                : new static(new Route, new RouteCollection())
            ;

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
    public function match(string $pattern, callable $to = null): RouteEntity
    {
        $route = clone $this->defaultRoute;
        $route->setPath($pattern);
        $this->controllers[] = $controller = new RouteEntity($route);
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
    public function get(string $pattern, callable $to = null): RouteEntity
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
    public function post(string $pattern, callable $to = null): RouteEntity
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
    public function put(string $pattern, callable $to = null): RouteEntity
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
    public function delete(string $pattern, callable $to = null): RouteEntity
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
    public function options(string $pattern, callable $to = null): RouteEntity
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
    public function patch(string $pattern, callable $to = null): RouteEntity
    {
        return $this->match($pattern, $to)->method('PATCH');
    }

    /**
     * Sets the requirement for a route variable for all current and future routes.
     *
     * @param string $variable
     * @param string $regexp
     * @return ControllerCollection
     */
    public function assert(string $variable, string $regexp): ControllerCollection
    {
        $this->defaultRoute->assert($variable, $regexp);

        foreach ( $this->controllers as $controller ) {
            $controller->assert($variable, $regexp);
        }

        return $this;
    }

    /**
     * Sets the default value for a route variable for all current and future routes.
     *
     * @param string $variable
     * @param $default
     * @return ControllerCollection
     */
    public function value(string $variable, $default): ControllerCollection
    {
        $this->defaultRoute->value($variable, $default);

        foreach ( $this->controllers as $controller ) {
            $controller->value($variable, $default);
        }

        return $this;
    }

    /**
     * Sets a converter for a route variable for all current and future routes.
     *
     * @param string $variable
     * @param callable $callback
     * @return ControllerCollection
     */
    public function convert(string $variable, callable $callback): ControllerCollection
    {
        $this->defaultRoute->convert($variable, $callback);

        foreach ( $this->controllers as $controller ) {
            $controller->convert($variable, $callback);
        }

        return $this;
    }

    /**
     * Sets the requirement for the HTTP method for all current and future routes.
     *
     * @param string $method
     * @return ControllerCollection
     */
    public function method(string $method): ControllerCollection
    {
        $this->defaultRoute->method($method);

        foreach ( $this->controllers as $controller ) {
            $controller->method($method);
        }

        return $this;
    }

    /**
     * Sets the requirement of hosts for all current and future routes.
     *
     * @param string $host
     * @return ControllerCollection
     */
    public function host(string $host): ControllerCollection
    {
        $this->defaultRoute->host($host);

        foreach ( $this->controllers as $controller ) {
            $controller->host($host);
        }

        return $this;
    }

    /**
     * Sets the requirement for HTTP (no HTTPS) for all current and future routes.
     *
     * @return ControllerCollection
     */
    public function requireHttp(): ControllerCollection
    {
        $this->defaultRoute->requireHttp();

        foreach ( $this->controllers as $controller ) {
            $controller->requireHttp();
        }

        return $this;
    }

    /**
     * Sets the requirement for HTTPS for all current and future routes.
     *
     * @return ControllerCollection
     */
    public function requireHttps(): ControllerCollection
    {
        $this->defaultRoute->requireHttps();

        foreach ( $this->controllers as $controller ) {
            $controller->requireHttps();
        }

        return $this;
    }

    /**
     * Sets a callback to handle before triggering the route callback for all current and future routes.
     *
     * @param callable $callback
     * @return ControllerCollection
     */
    public function before(callable $callback): ControllerCollection
    {
        $this->defaultRoute->before($callback);

        foreach ( $this->controllers as $controller ) {
            $controller->before($callback);
        }

        return $this;
    }

    /**
     * Sets a callback to handle after the route callback for all current and future routes.
     *
     * @param callable $callback
     * @return ControllerCollection
     */
    public function after(callable $callback): ControllerCollection
    {
        $this->defaultRoute->after($callback);

        foreach ( $this->controllers as $controller ) {
            $controller->after($callback);
        }

        return $this;
    }

    /**
     * Sets a condition for the route to match for all current and future routes.
     *
     * @param string $condition
     * @return ControllerCollection
     */
    public function when(string $condition): ControllerCollection
    {
        $this->defaultRoute->when($condition);

        foreach ( $this->controllers as $controller ) {
            $controller->when($condition);
        }

        return $this;
    }
}