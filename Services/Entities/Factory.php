<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Entities;


use Descent\Services\Abstracts\AbstractService;

/**
 * Class Factory
 *
 * @package Descent\Services\Entities
 */
class Factory extends AbstractService
{
    /**
     * Factory constructor.
     *
     * @param string $interface
     * @param \Closure $factory
     */
    public function __construct(string $interface, \Closure $factory)
    {
        $this->interface = $interface;
        $this->concrete = $factory;
    }
}