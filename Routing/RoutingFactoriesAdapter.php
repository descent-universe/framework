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


/**
 * Class RoutingFactoriesAdapter
 * @package Descent\Routing
 */
class RoutingFactoriesAdapter
{
    /**
     * @var callable
     */
    protected $routeFactory;

    /**
     * @var callable
     */
    protected $routeCollectionFactory;

    /**
     * @var callable
     */
    protected $routeEntityFactory;

    /**
     * @var callable
     */
    protected $controllerFactory;

    /**
     * @var callable
     */
    protected $controllerCollectionFactory;

    /**
     * sets the route factory callback.
     *
     * @param callable $callback
     * @return RoutingFactoriesAdapter
     */
    public function withRouteFactory(callable $callback): RoutingFactoriesAdapter
    {
        $this->routeFactory = $callback;

        return $this;
    }

    /**
     * sets the route collection factory callback.
     *
     * @param callable $callback
     * @return RoutingFactoriesAdapter
     */
    public function withRouteCollectionFactory(callable $callback): RoutingFactoriesAdapter
    {
        $this->routeCollectionFactory = $callback;

        return $this;
    }

    /**
     * sets the route entity factory callback.
     *
     * @param callable $callback
     * @return RoutingFactoriesAdapter
     */
    public function withRouteEntityFactory(callable $callback): RoutingFactoriesAdapter
    {
        $this->routeEntityFactory = $callback;

        return $this;
    }

    /**
     * sets the controller factory callback.
     *
     * @param callable $callback
     * @return RoutingFactoriesAdapter
     */
    public function withControllerFactory(callable $callback): RoutingFactoriesAdapter
    {
        $this->controllerFactory = $callback;

        return $this;
    }

    /**
     * sets the controller collection factory callback.
     *
     * @param callable $callback
     * @return RoutingFactoriesAdapter
     */
    public function withControllerCollectionFactory(callable $callback): RoutingFactoriesAdapter
    {
        $this->controllerCollectionFactory = $callback;

        return $this;
    }

    /**
     * creates a route.
     *
     * @param array ...$parameters
     * @return Route
     */
    public function createRoute(... $parameters): Route
    {
        return call_user_func_array($this->routeFactory, $parameters);
    }

    /**
     * creates a route collection.
     *
     * @param array ...$parameters
     * @return RouteCollection
     */
    public function createRouteCollection(... $parameters): RouteCollection
    {
        return call_user_func_array($this->routeCollectionFactory, $parameters);
    }

    /**
     * creates a route entity.
     *
     * @param array ...$parameters
     * @return RouteEntity
     */
    public function createRouteEntity(... $parameters): RouteEntity
    {
        return call_user_func_array($this->routeEntityFactory, $parameters);
    }

    /**
     * creates a controller.
     *
     * @param array ...$parameters
     * @return Controller
     */
    public function createController(... $parameters): Controller
    {
        return call_user_func_array($this->controllerFactory, $parameters);
    }

    /**
     * creates a controller collection.
     *
     * @param array ...$parameters
     * @return ControllerCollection
     */
    public function createControllerCollection(... $parameters): ControllerCollection
    {
        return call_user_func_array($this->controllerCollectionFactory, $parameters);
    }
}