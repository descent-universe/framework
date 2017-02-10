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


interface ProviderInterface
{
    /**
     * ProviderInterface constructor for options delivery.
     * @param array $options
     */
    public function __construct(array $options = []);

    /**
     * factory method.
     *
     * @param array $options
     * @return mixed
     */
    public static function create(array $options = []);
}