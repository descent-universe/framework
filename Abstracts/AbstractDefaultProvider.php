<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Abstracts;


use Descent\Contracts\ProviderInterface;
use Descent\Contracts\ServiceProviderInterface;

abstract class AbstractDefaultProvider implements ProviderInterface, ServiceProviderInterface
{
    use ProviderTrait;


}