<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Contracts;


use Descent\Services\Contracts\ServiceContainerInterface;

/**
 * Interface ServiceProviderInterface
 *
 * @package Descent\Contracts
 */
interface ServiceProviderInterface
{
    /**
     * command for container manipulation.
     *
     * @param ServiceContainerInterface $container
     */
    public function services(ServiceContainerInterface $container);
}