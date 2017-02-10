<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Exceptions;


use Descent\Services\Contracts\ServiceExceptionInterface;
use LogicException;

class NotFoundException extends LogicException implements ServiceExceptionInterface
{

}