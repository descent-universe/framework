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
use Descent\Contracts\RoutesInterface;
use Descent\Routing\ControllerCollection;
use Descent\Routing\Route;
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
        /**
         * Controllers Collection
         *
         * @return ControllerCollection
         */
        $controllersFactory = function() use (&$controllersFactory): ControllerCollection {
            return new ControllerCollection(new Route(), new RouteCollection(), $controllersFactory);
        };


    }

}