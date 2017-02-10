<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Contracts;


interface ServiceInterface
{
    /**
     * returns the service interface.
     *
     * @return string
     */
    public function getInterface(): string;

    /**
     * returns the concrete.
     *
     * @return string|\Closure
     */
    public function getConcrete();

    /**
     * defines the singleton state of the service.
     *
     * @param bool $flag
     * @return ServiceInterface
     */
    public function singleton(bool $flag = true): ServiceInterface;

    /**
     * checks whether the incubated instance will be served as an instance or not.
     *
     * @return bool
     */
    public function isSingleton(): bool;

    /**
     * binds parameter assignments to the service.
     *
     * @param array $parameters
     * @return ServiceInterface
     */
    public function withParameters(array $parameters): ServiceInterface;

    /**
     * returns the parameter bindings of the service.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * binds the optional parameters to be resolved.
     *
     * @param \string[] ...$parameters
     * @return ServiceInterface
     */
    public function enforceParameters(string ... $parameters): ServiceInterface;

    /**
     * returns an array of optional parameters being resolved.
     *
     * @return string[]
     */
    public function getEnforcedParameters(): array;
}