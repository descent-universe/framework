<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Facades\Abstracts;


use Descent\Facades\Contracts\FacadeInterface;
use Descent\Facades\Exceptions\ContainmentException;
use Descent\Services\Contracts\ServiceContainerInterface;

/**
 * Class AbstractFacade
 *
 * @package Descent\Facades\Abstracts
 */
abstract class AbstractFacade implements FacadeInterface
{
    /**
     * @var ServiceContainerInterface
     */
    private static $container;

    /**
     * returns the interface of the facade.
     *
     * @return string
     */
    abstract public static function getInterface(): string;

    /**
     * returns the blocked methods of the facade.
     *
     * @return array
     */
    public static function getBlockedMethods(): array
    {
        return [];
    }

    /**
     * sets the service container instance.
     *
     * @param ServiceContainerInterface $container
     * @return void
     */
    public static function setContainer(ServiceContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * magic static caller.
     *
     * @param $name
     * @param array $arguments
     * @throws ContainmentException
     * @return mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        if ( ! self::$container instanceof ServiceContainerInterface ) {
            throw new ContainmentException(
                'No container assigned, check your facade providers'
            );
        }

        if ( ! self::$container->has(static::getInterface()) ) {
            throw new ContainmentException(
                'Assigned container is not aware of the facade interface: '.static::getInterface()
            );
        }

        $instance = self::$container->make(static::getInterface());

        if ( ! method_exists($instance, $name) ) {
            throw new ContainmentException(
                'Facade interface is not aware of the called method: '.$name
            );
        }

        $method = strtolower($name);

        if ( in_array($name, static::getBlockedMethods()) ) {
            throw new ContainmentException(
                'method is blocked for this facade: '.$method
            );
        }

        return call_user_func_array([$instance, $name], $arguments);
    }
}