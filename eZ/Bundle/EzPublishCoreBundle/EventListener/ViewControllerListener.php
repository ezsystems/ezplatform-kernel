<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilderRegistry;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use Ibexa\Contracts\Core\Event\View\PostBuildViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewControllerListener implements EventSubscriberInterface
{
    /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface */
    private $controllerResolver;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilderRegistry */
    private $viewBuilderRegistry;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    private $eventDispatcher;

    public function __construct(
        ControllerResolverInterface $controllerResolver,
        ViewBuilderRegistry $viewBuilderRegistry,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->controllerResolver = $controllerResolver;
        $this->viewBuilderRegistry = $viewBuilderRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => ['getController', 10]];
    }

    /**
     * Configures the View for eZ View controllers.
     *
     * @param \Symfony\Component\HttpKernel\Event\ControllerEvent $event
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function getController(ControllerEvent $event)
    {
        $request = $event->getRequest();

        if (($viewBuilder = $this->viewBuilderRegistry->getFromRegistry($request->attributes->get('_controller'))) === null) {
            return;
        }

        $parameterEvent = new FilterViewBuilderParametersEvent(clone $request);
        $this->eventDispatcher->dispatch($parameterEvent, ViewEvents::FILTER_BUILDER_PARAMETERS);
        $view = $viewBuilder->buildView($parameterEvent->getParameters()->all());
        $this->eventDispatcher->dispatch(new PostBuildViewEvent($view));
        $request->attributes->set('view', $view);

        // View parameters are added as request attributes so that they are available as controller action parameters
        $request->attributes->add($view->getParameters());

        if (($controllerReference = $view->getControllerReference()) instanceof ControllerReference) {
            $request->attributes->set('_controller', $controllerReference->controller);
            $event->setController($this->controllerResolver->getController($request));
        }
    }
}
