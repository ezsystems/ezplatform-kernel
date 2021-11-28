<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\Builder\ParametersFilter;

use Ibexa\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Collects parameters for the ViewBuilder from the Request.
 */
class RequestAttributes implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [ViewEvents::FILTER_BUILDER_PARAMETERS => 'addRequestAttributes'];
    }

    /**
     * Adds all the request attributes to the parameters.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent $e
     */
    public function addRequestAttributes(FilterViewBuilderParametersEvent $e)
    {
        $parameterBag = $e->getParameters();
        $parameterBag->add($e->getRequest()->attributes->all());

        // maybe this should be in its own listener ? The ViewBuilder needs it.
        if (!$parameterBag->has('viewType')) {
            $parameterBag->add(['viewType' => null]);
        }
    }
}

class_alias(RequestAttributes::class, 'eZ\Publish\Core\MVC\Symfony\View\Builder\ParametersFilter\RequestAttributes');
