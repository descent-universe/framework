<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Bootstrap;


use Descent\Abstracts\AbstractDefaultProvider;
use Descent\Contracts\EventProviderInterface;
use Descent\Resolvers\Kernel\ControllerArgumentResolver;
use Descent\Services\Contracts\ServiceContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelProvider extends AbstractDefaultProvider implements EventProviderInterface
{
    /**
     * factory method.
     *
     * @param array $options
     * @return mixed
     */
    public static function create(array $options = [])
    {
        return new static($options);
    }

    /**
     * command for container manipulation.
     *
     * @param ServiceContainerInterface $container
     */
    public function services(ServiceContainerInterface $container)
    {
        /**
         * Event Dispatcher
         */
        $container->bind(EventDispatcherInterface::class, EventDispatcher::class)->singleton();

        /**
         * Request Stack
         */
        $container->bind(RequestStack::class)->singleton();

        /**
         * Http Kernel
         */
        $container
            ->bind(HttpKernelInterface::class, HttpKernel::class)
            ->singleton()
            ->enforceParameters(
                'requestStack',
                'argumentResolver'
            )
        ;

        /**
         * Controller Resolver
         */
        $container->bind(ControllerResolverInterface::class, ControllerResolver::class)->singleton();

        /**
         * Controller Argument Resolver
         */
        $container
            ->bind(ArgumentResolverInterface::class, ControllerArgumentResolver::class)
            ->singleton()
            ->withParameters([$container])
        ;
    }

    /**
     * command for event dispatcher manipulation
     *
     * @param EventDispatcherInterface $events
     */
    public function events(EventDispatcherInterface $events)
    {
        $events->addSubscriber(new ResponseListener($this->options('charset') ?? 'utf-8'));
    }
}