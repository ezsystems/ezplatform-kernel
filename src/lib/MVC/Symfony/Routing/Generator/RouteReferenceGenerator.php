<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Routing\Generator;

use Ibexa\Core\MVC\Symfony\Event\RouteReferenceGenerationEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\RequestStackAware;
use Ibexa\Core\MVC\Symfony\Routing\RouteReference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class RouteReferenceGenerator implements RouteReferenceGeneratorInterface
{
    use RequestStackAware;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Generates a RouteReference, based on the given resource and associated params.
     * If $resource is null, generated route reference will be based on current request's route and parameters.
     *
     * @param mixed $resource The route name. Can be any resource supported by the different routers (e.g. Location object).
     * @param array $params Array of parameters, used to generate the final link along with $resource.
     *
     * @return \Ibexa\Core\MVC\Symfony\Routing\RouteReference
     */
    public function generate($resource = null, array $params = [])
    {
        $request = $this->getCurrentRequest();
        if ($resource === null) {
            $resource = $request->attributes->get('_route');
            $params += $request->attributes->get('_route_params', []);
        }

        $event = new RouteReferenceGenerationEvent(new RouteReference($resource, $params), $request);
        $this->dispatcher->dispatch($event, MVCEvents::ROUTE_REFERENCE_GENERATION);

        return $event->getRouteReference();
    }
}

class_alias(RouteReferenceGenerator::class, 'eZ\Publish\Core\MVC\Symfony\Routing\Generator\RouteReferenceGenerator');
