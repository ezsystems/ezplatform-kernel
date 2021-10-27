<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Event;

use Ibexa\Core\MVC\Symfony\Event\RouteReferenceGenerationEvent;
use Ibexa\Core\MVC\Symfony\Routing\RouteReference;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteReferenceGenerationEventTest extends TestCase
{
    public function testConstruct()
    {
        $routeReference = new RouteReference('foo');
        $request = new Request();
        $event = new RouteReferenceGenerationEvent($routeReference, $request);
        $this->assertSame($routeReference, $event->getRouteReference());
        $this->assertSame($request, $event->getRequest());
    }

    public function testGetSet()
    {
        $routeReference = new RouteReference('foo');
        $request = new Request();

        $event = new RouteReferenceGenerationEvent($routeReference, $request);
        $this->assertSame($routeReference, $event->getRouteReference());
        $this->assertSame($request, $event->getRequest());

        $newRouteReference = new RouteReference('bar');
        $event->setRouteReference($newRouteReference);
        $this->assertSame($newRouteReference, $event->getRouteReference());
    }
}

class_alias(RouteReferenceGenerationEventTest::class, 'eZ\Publish\Core\MVC\Symfony\Event\Tests\RouteReferenceGenerationEventTest');
