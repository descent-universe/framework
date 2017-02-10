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


use Descent\Routing\Exceptions\RoutingException;

/**
 * Class RouteEntity
 *
 * @package Descent\Routing
 */
class RouteEntity
{
    /**
     * @var Route
     */
    private $route;

    /**
     * @var string
     */
    private $routeName = '';

    /**
     * @var bool
     */
    private $locked = false;

    /**
     * RouteEntity constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * returns the Route instance of this Entity.
     *
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * returns the Route name of this Entity.
     *
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * binds a new route name to this Entity.
     *
     * @param string $name
     * @return RouteEntity
     */
    public function bind(string $name): RouteEntity
    {
        if ( $this->locked ) {
            throw new RoutingException(
                'You can not longer modify the route entity name'
            );
        }

        $this->routeName = $name;

        return $this;
    }

    /**
     * locks this entity.
     *
     * @return RouteEntity
     */
    public function lock(): RouteEntity
    {
        $this->locked = true;

        return $this;
    }

    /**
     * generates a route name utilizing the provided prefix.
     *
     * @param $prefix
     * @return string
     */
    public function generateRouteName($prefix): string
    {
        $methods = $this->route->getMethods();
        $path = $this->route->getPath();

        $routeName = "_{$methods}_{$prefix}{$path}";
        $routeName = str_replace(['/', ':', '|', '-'], '_', $routeName);
        $routeName = preg_replace('/[^a-z0-9_.]+/i', '', $routeName);
        $routeName = preg_replace('/_+/', '_', $routeName);

        return $routeName;
    }

    /**
     * Sets the requirement for a route variable
     *
     * @param string $variable
     * @param string $regexp
     * @return RouteEntity
     */
    public function assert(string $variable, string $regexp): RouteEntity
    {
        $this->route->assert($variable, $regexp);

        return $this;
    }

    /**
     * Sets the default value for a route variable.
     *
     * @param string $variable
     * @param $default
     * @return RouteEntity
     */
    public function value(string $variable, $default): RouteEntity
    {
        $this->route->value($variable, $default);

        return $this;
    }

    /**
     * Sets a converter for a route variable.
     *
     * @param string $variable
     * @param callable $callback
     * @return RouteEntity
     */
    public function convert(string $variable, callable $callback): RouteEntity
    {
        $this->route->convert($variable, $callback);

        return $this;
    }

    /**
     * Sets the requirement for the HTTP method on this Entity.
     *
     * @param string $method
     * @return RouteEntity
     */
    public function method(string $method): RouteEntity
    {
        $this->route->method($method);

        return $this;
    }

    /**
     * Sets the requirement of hosts on this Entity.
     *
     * @param string $host
     * @return RouteEntity
     */
    public function host(string $host): RouteEntity
    {
        $this->route->host($host);

        return $this;
    }

    /**
     * Sets the requirement for HTTP (no HTTPS) on this Entity.
     *
     * @return RouteEntity
     */
    public function requireHttp(): RouteEntity
    {
        $this->route->requireHttp();

        return $this;
    }

    /**
     * Sets the requirement for HTTPS on this Entity.
     *
     * @return RouteEntity
     */
    public function requireHttps(): RouteEntity
    {
        $this->route->requireHttps();

        return $this;
    }

    /**
     * Sets a callback to handle before triggering the route callback on this entity.
     *
     * @param callable $callback
     * @return RouteEntity
     */
    public function before(callable $callback): RouteEntity
    {
        $this->route->before($callback);

        return $this;
    }

    /**
     * Sets a callback to handle before the route callback on this entity.
     *
     * @param callable $callback
     * @return RouteEntity
     */
    public function after(callable $callback): RouteEntity
    {
        $this->route->after($callback);

        return $this;
    }

    /**
     * Sets a condition for the route to match on this entity.
     *
     * @param string $condition
     * @return RouteEntity
     */
    public function when(string $condition): RouteEntity
    {
        $this->route->when($condition);

        return $this;
    }
}