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


use Descent\Routing\Exceptions\FrozenControllerException;
use Descent\Routing\Exceptions\IncompatibleBindingException;

/**
 * Class Controller
 *
 * @package Descent\Routing
 *
 * @method Controller assert(string $variable, string $regexp)
 * @method Controller value(string $variable, mixed $default)
 * @method Controller convert(string $variable, callable $callback)
 * @method Controller method(string $method)
 * @method Controller requireHttp()
 * @method Controller requireHttps()
 * @method Controller before(callable $callback)
 * @method Controller after(callable $callback)
 * @method Controller when(string $condition)
 */
class Controller
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
    private $isFrozen = false;

    /**
     * Controller constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * returns the Route in charge.
     *
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * returns the bound route name.
     *
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * bind the provided route name.
     *
     * @param string $routeName
     * @throws FrozenControllerException
     * @throws IncompatibleBindingException
     * @return Controller
     */
    public function bind(string $routeName): Controller
    {
        if ( $this->isFrozen ) {
            throw new FrozenControllerException(
                sprintf(
                    'Calling %s frozen %s instance',
                    __METHOD__,
                    __CLASS__
                )
            );
        }

        $routeName = $this->normalizeRouteName($routeName);

        if ( empty($routeName) ) {
            throw new IncompatibleBindingException(
                'route name bindings can not be done with empty strings'
            );
        }

        $this->routeName = $routeName;

        return $this;
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array $arguments
     * @return Controller
     */
    public function __call(string $method, array $arguments = []): Controller
    {
        if ( ! method_exists($this->route, $method) ) {
            throw new \BadMethodCallException(
                sprintf(
                    'Method "%s::%s" does not exist.',
                    get_class($this->route),
                    $method
                )
            );
        }

        call_user_func_array([$this->route, $method], $arguments);

        return $this;
    }

    /**
     * freezes the current container.
     */
    public function freeze()
    {
        $this->isFrozen = true;
    }

    /**
     * generates a controller-aware route name.
     *
     * @param string $prefix
     * @return string
     */
    public function generateRouteName(string $prefix): string
    {
        $methods = implode('_', $this->route->getMethods()).'_';

        $routeName = $methods.$prefix.$this->route->getPath();

        return $this->normalizeRouteName($routeName);
    }

    /**
     * normalizes the provided route name.
     *
     * @param string $routeName
     * @return string
     */
    protected function normalizeRouteName(string $routeName): string
    {
        $routeName = str_replace(['/', ':', '|', '-', ' '], '_', $routeName);
        $routeName = preg_replace('/[^a-z0-9_.]+/i', '', $routeName);
        $routeName = preg_replace('/_+/', '_', $routeName);

        return $routeName;
    }
}