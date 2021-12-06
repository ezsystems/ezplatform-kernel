<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\ParametersInjector;

use Ibexa\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Injects into a View parameters that were collected via the EventDispatcher.
 */
class EventDispatcherInjector implements ParametersInjector
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function injectViewParameters(View $view, array $parameters)
    {
        $event = new FilterViewParametersEvent($view, $parameters);
        $this->eventDispatcher->dispatch($event, ViewEvents::FILTER_VIEW_PARAMETERS);
        $view->addParameters($event->getViewParameters());
    }
}

class_alias(EventDispatcherInjector::class, 'eZ\Publish\Core\MVC\Symfony\View\ParametersInjector\EventDispatcherInjector');
