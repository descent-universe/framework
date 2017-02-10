<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Abstracts;


use Descent\Services\Contracts\ServiceInterface;

/**
 * Class AbstractService
 *
 * @package Descent\Services\Abstracts
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * @var string
     */
    protected $interface;

    /**
     * @var string|\Closure
     */
    protected $concrete;

    /**
     * @var bool
     */
    protected $isSingleton = false;

    /**
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * @var string[]
     */
    protected $enforcedParameters = [];

    /**
     * returns the service interface.
     *
     * @return string
     */
    public function getInterface(): string
    {
        return $this->interface;
    }

    /**
     * returns the concrete.
     *
     * @return string|\Closure
     */
    public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * defines the singleton state of the service.
     *
     * @param bool $flag
     * @return ServiceInterface
     */
    public function singleton(bool $flag = true): ServiceInterface
    {
        $this->isSingleton = $flag;

        return $this;
    }

    /**
     * checks whether the incubated instance will be served as an instance or not.
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->isSingleton;
    }

    /**
     * binds parameter assignments to the service.
     *
     * @param array $parameters
     * @return ServiceInterface
     */
    public function withParameters(array $parameters): ServiceInterface
    {
        foreach ( $parameters as $key => $value ) {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    /**
     * returns the parameter bindings of the service.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * binds the optional parameters to be resolved.
     *
     * @param \string[] ...$parameters
     * @return ServiceInterface
     */
    public function enforceParameters(string ... $parameters): ServiceInterface
    {
        foreach ( $parameters as $parameter ) {
            if ( ! in_array($parameter, $this->enforcedParameters) ) {
                $this->enforcedParameters[] = $parameter;
            }
        }

        return $this;
    }

    /**
     * returns an array of optional parameters being resolved.
     *
     * @return string[]
     */
    public function getEnforcedParameters(): array
    {
        return $this->enforcedParameters;
    }

}