<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Facades;


use Descent\Facades\Abstracts\AbstractFacade;
use Descent\Services\Contracts\ServiceContainerInterface;
use Descent\Services\Contracts\ServiceInterface;
use Descent\Contracts\ServiceProviderInterface;

/**
 * Static Facade Services
 *
 * @package Descent\Facades
 *
 * @method static ServiceInterface get(string $interface)
 * @method static bool has(string ... $interface)
 * @method static ServiceInterface bind(string $interface, $concrete = null)
 * @method static ServiceInterface factory(string $interface, callable $callback)
 * @method static make(string $interface, array $parameters = [], string ... $enforcedOptionalParameters)
 * @method static mixed call(callable $callback, array $parameters = [], string ... $enforcedOptionalParameters)
 * @method static ServiceContainerInterface split(string ... $interfaces)
 * @method static ServiceContainerInterface expel(string ... $interfaces)
 * @method static register(ServiceProviderInterface ... $providers)
 */
class Services extends AbstractFacade
{
    /**
     * returns the interface of the facade.
     *
     * @return string
     */
    public static function getInterface(): string
    {
        return ServiceContainerInterface::class;
    }

    /**
     * returns the blocked methods of the facade.
     *
     * @return array
     */
    public static function getBlockedMethods(): array
    {
        return [
            'get',
            'bind',
            'factory',
            'register',
        ];
    }


}