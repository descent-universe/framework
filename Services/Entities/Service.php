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
 * Class Service
 *
 * @package Descent\Services\Entities
 */
class Service extends AbstractService
{
    /**
     * Service constructor.
     *
     * @param string $interface
     * @param string $concrete
     */
    public function __construct(string $interface, string $concrete)
    {
        $this->interface = $interface;
        $this->concrete = $concrete;
    }
}