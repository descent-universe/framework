<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Resolvers\Kernel;


use Descent\Services\Container;
use Descent\Services\Contracts\ServiceContainerInterface;
use Descent\Services\Exceptions\ForgeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ControllerArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @var ServiceContainerInterface
     */
    private $services;

    public function __construct(ServiceContainerInterface $services)
    {
        $this->services = $services;
    }

    /**
     * Returns the arguments to pass to the controller.
     *
     * @param Request $request
     * @param callable $controller
     *
     * @return array An array of arguments to pass to the controller
     *
     * @throws \RuntimeException When no value could be provided for a required argument
     */
    public function getArguments(Request $request, $controller)
    {
        $closure = Container::marshalFactory($controller);

        $arguments = [];

        foreach ( (new \ReflectionFunction($closure))->getParameters() as $parameter ) {
            /**
             * fulfill POST parameters. Fulfill type hint by auto-cast
             */
            if ( $parameter->hasType() && $request->request->has($parameter->getName()) ) {
                $injectData = $request->request->get($parameter->getName());

                if ( $parameter->getType()->isBuiltin() ) {
                    settype($injectData, (string) $parameter->getType());
                }

                $arguments[$parameter->getPosition()] = $injectData;
                continue;
            }

            if ( ! $parameter->hasType() && ! $parameter->getClass() && $request->request->has($parameter->getName()) ) {
                $arguments[$parameter->getPosition()] = $request->request->get($parameter->getName());
                continue;
            }

            /**
             * fulfill GET parameters. Fulfill type hint by auto-cast
             */
            if ( $parameter->hasType() && $request->query->has($parameter->getName()) ) {
                $injectData = $request->query->get($parameter->getName());

                if ( $parameter->getType()->isBuiltin() ) {
                    settype($injectData, (string) $parameter->getType());
                }

                $argument[$parameter->getPosition()] = $injectData;
                continue;
            }

            if ( ! $parameter->hasType() && ! $parameter->getClass() && $request->query->has($parameter->getName()) ) {
                $arguments[$parameter->getPosition()] = $request->query->get($parameter->getName());
            }

            /**
             * fulfill Request object dependency
             */
            if ( $parameter->getClass() && $parameter->getClass()->isInstance($request) ) {
                $arguments[$parameter->getPosition()] = $request;
                continue;
            }

            /**
             * fulfill class dependencies
             */
            if ( $parameter->getClass() && $this->services->has($parameter->getClass()->getName()) ) {
                $arguments[$parameter->getPosition()] = $this->services->make($parameter->getClass()->getName());
                continue;
            }

            /**
             * fulfill default value
             */
            if ( $parameter->isOptional() && $parameter->isDefaultValueAvailable() ) {
                $arguments[$parameter->getPosition()] = $parameter->getDefaultValue();
                continue;
            }

            /**
             * otherwise throw an exception
             */
            throw new ForgeException(
                'Unable to resolve controller dependency: '.$parameter->getName()
            );
        }

        return $arguments;
    }

}