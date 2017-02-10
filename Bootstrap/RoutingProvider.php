<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Bootstrap;


use Descent\Abstracts\AbstractDefaultProvider;
use Descent\Routing\Controller;
use Descent\Routing\ControllerCollection;
use Descent\Routing\Exceptions\RoutingException;
use Descent\Routing\Route;
use Descent\Routing\RoutingFactoriesAdapter;
use Descent\Services\Contracts\ServiceContainerInterface;
use Symfony\Component\Routing\RouteCollection;

class RoutingProvider extends AbstractDefaultProvider
{
    /**
     * returns the settings group.
     *
     * @return string
     */
    public function getSettingsGroup(): string
    {
        return 'framework.routing';
    }

    /**
     * command for container manipulation.
     *
     * @param ServiceContainerInterface $container
     */
    public function services(ServiceContainerInterface $container)
    {
        $routingFactories = new RoutingFactoriesAdapter();

        /**
         * Route
         */
        $routingFactories->withRouteFactory(function() use ($container): Route {
            $routeClass = $this->options('objects.route.class') ?? Route::class;

            if ( ! is_a($routeClass, Route::class, true) ) {
                throw new RoutingException(
                    'Route class must be a subclass of '.Route::class
                );
            }

            $parameters = $this->options('object.route.parameters') ?? [];

            if ( ! is_array($parameters) ) {
                throw new RoutingException(
                    'Route class parameter definition must be an array'
                );
            }

            $enforcedParameters = $this->options('object.route.enforcedParameters') ?? [];

            if ( ! is_array($enforcedParameters) ) {
                throw new RoutingException(
                    'Route class enforced parameter definition must be an array'
                );
            }

            return $container->make($routeClass, $parameters, ... array_values($enforcedParameters));
        });

        /**
         * Route collection
         */
        $routingFactories->withRouteFactory(function() use ($container): RouteCollection {
            $routeCollectionClass = $this->options('objects.routeCollection.class') ?? RouteCollection::class;

            if ( ! is_a($routeCollectionClass, RouteCollection::class, true) ) {
                throw new RoutingException(
                    'Route collection class must be a subclass of '.RouteCollection::class
                );
            }

            $parameters = $this->options('object.routeCollection.parameters') ?? [];

            if ( ! is_array($parameters) ) {
                throw new RoutingException(
                    'Route collection class parameter definition must be an array'
                );
            }

            $enforcedParameters = $this->options('object.routeCollection.enforcedParameters') ?? [];

            if ( ! is_array($enforcedParameters) ) {
                throw new RoutingException(
                    'Route collection class enforced parameter definition must be an array'
                );
            }

            return $container->make($routeCollectionClass, $parameters, ... array_values($enforcedParameters));
        });

        /**
         * Controller
         */
        $routingFactories->withControllerFactory(function(Route $route) use ($container) {
            $controllerClass = $this->options('objects.controller.class') ?? Controller::class;

            if ( ! is_a($controllerClass, Controller::class, true) ) {
                throw new RoutingException(
                    'Controller class must be a subclass of '.Controller::class
                );
            }

            $parameters = $this->options('objects.controller.parameters') ?? [];

            if ( ! is_array($parameters) ) {
                throw new RoutingException(
                    'Controller class parameter definition must be an array'
                );
            }

            $enforcedParameters = $this->options('objects.controller.enforcedParameters') ?? [];

            if ( ! is_array($enforcedParameters) ) {
                throw new RoutingException(
                    'Controller class enforced parameter definition must be an array'
                );
            }

            return $container->make($controllerClass, $parameters, ... array_values($enforcedParameters));
        });

        /**
         * Controllers Collection
         *
         * @return ControllerCollection
         */
        $routingFactories->withControllerCollectionFactory(
            function(Route $route = null, RouteCollection $routeCollection = null) use ($routingFactories) {
                if ( ! $route instanceof Route ) {
                    $route = $routingFactories->createRoute();
                }

                if ( ! $routeCollection instanceof RouteCollection::class ) {
                    $routeCollection = $routingFactories->createRouteCollection();
                }

                return $routingFactories->createControllerCollection(
                    $route,
                    $routeCollection,
                    $routingFactories
                );
            }
        );

        $container->bind(RoutingFactoriesAdapter::class, $routingFactories);

        $container->factory(RouteCollection::class, function(RoutingFactoriesAdapter $routingFactories) {
            return $routingFactories->createRouteCollection();
        })->singleton();

        $container->factory(ControllerCollection::class, function(RoutingFactoriesAdapter $routingFactories) {
            return $routingFactories->createRouteCollection();
        })->singleton();
    }

}