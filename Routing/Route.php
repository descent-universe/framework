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


use Symfony\Component\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    /**
     * Constructor.
     *
     * Available options:
     *
     *  * compiler_class: A class name able to compile this route instance (RouteCompiler by default)
     *  * utf8:           Whether UTF-8 matching is enforced ot not
     *
     * @param string $path The path pattern to match
     * @param array $defaults An array of default parameter values
     * @param array $requirements An array of requirements for parameters (regexes)
     * @param array $options An array of options
     * @param string $host The host pattern to match
     * @param string|array $schemes A required URI scheme or an array of restricted schemes
     * @param string|array $methods A required HTTP method or an array of restricted methods
     * @param string $condition A condition that should evaluate to true for the route to match
     */
    public function __construct($path = '/', array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array(), $condition = '')
    {
        parent::__construct($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }

    /**
     * Sets the route code that should be executed when matched.
     *
     * @param callable $to callback that returns the response when matched
     * @return Route $this The current Route instance
     */
    public function run(callable $to): Route
    {
        $this->setDefault('_controller', $to);

        return $this;
    }

    /**
     * Sets the requirement for a route variable
     *
     * @param string $variable The variable name
     * @param string $regexp The regular expression to apply
     * @return Route $this The current Route instance
     */
    public function assert(string $variable, string $regexp): Route
    {
        $this->setRequirement($variable, $regexp);

        return $this;
    }

    /**
     * Sets the default value for a route variable.
     *
     * @param string $variable the variable name
     * @param mixed $default the default value
     * @return Route $this The current Route instance
     */
    public function value(string $variable, $default): Route
    {
        $this->setDefault($variable, $default);

        return $this;
    }

    /**
     * Sets a converter for a route variable.
     *
     * @param string $variable The variable name
     * @param callable $callback a callback that converts the original value
     * @return Route $this The current Route instance
     */
    public function convert(string $variable, callable $callback): Route
    {
        $converters = $this->getOption('_converters');
        $converters[$variable] = $callback;
        $this->setOption('_converters', $converters);

        return $this;
    }

    /**
     * Sets the requirement for the HTTP method.
     *
     * @param string $method The HTTP method name. Multiple methods can be supplied, delimited by a pipe character.
     * @return Route $this The current Route instance
     */
    public function method(string $method): Route
    {
        $this->setMethods(explode('|', $method));

        return $this;
    }

    /**
     * Sets the requirement of hosts on this Route.
     *
     * @param string $host The host for which this route should be enabled
     * @return Route $this The current Route instance
     */
    public function host(string $host): Route
    {
        $this->setHost($host);

        return $this;
    }

    /**
     * Sets the requirement for HTTP (no HTTPS) on this Route.
     *
     * @return Route $this The current Route instance
     */
    public function requireHttp(): Route
    {
        $this->setSchemes('http');

        return $this;
    }

    /**
     * Sets the requirement for HTTPS on this Route.
     *
     * @return Route $this The current Route instance
     */
    public function requireHttps(): Route
    {
        $this->setSchemes('https');

        return $this;
    }

    /**
     * Sets a callback to handle before triggering the route callback.
     *
     * @param callable $callback a callback to be triggered when the Route is matched, just before the route callback
     * @return Route $this The current Route instance
     */
    public function before(callable $callback): Route
    {
        $current = $this->getOption('_before_middlewares');
        $current[] = $callback;
        $this->setOption('_before_middlewares', $current);

        return $this;
    }

    /**
     * Sets a callback to handle after the route callback.
     *
     * @param callable $callback a callback to be triggered after the route callback
     * @return Route $this The current Route instance
     */
    public function after(callable $callback): Route
    {
        $current = $this->getOption('after_middlewares');
        $current[] = $callback;
        $this->setOption('_after_middlewares', $current);

        return $this;
    }

    /**
     * Sets a condition for the route to match.
     *
     * @param string $condition
     * @return Route $this The current Route instance
     */
    public function when(string $condition): Route
    {
        $this->setCondition($condition);

        return $this;
    }
}