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


use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface EventProviderInterface
{
    /**
     * command for event dispatcher manipulation
     *
     * @param EventDispatcherInterface $events
     */
    public function events(EventDispatcherInterface $events);
}