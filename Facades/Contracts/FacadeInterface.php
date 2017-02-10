<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Facades\Contracts;


use Descent\Services\Contracts\ServiceContainerInterface;

interface FacadeInterface
{
    /**
     * returns the interface of the facade.
     *
     * @return string
     */
    public static function getInterface(): string;

    /**
     * returns the blocked methods of the facade.
     *
     * @return array
     */
    public static function getBlockedMethods(): array;

    /**
     * sets the service container instance.
     *
     * @param ServiceContainerInterface $container
     * @return void
     */
    public static function setContainer(ServiceContainerInterface $container);
}