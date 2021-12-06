<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Event;

use Ibexa\Contracts\Core\Repository\Events\URL\BeforeUpdateUrlEvent;
use Ibexa\Contracts\Core\Repository\Events\URL\UpdateUrlEvent;
use Ibexa\Contracts\Core\Repository\URLService as URLServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Ibexa\Core\Event\URLService;

class URLServiceTest extends AbstractServiceTest
{
    public function testUpdateUrlEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateUrlEvent::class,
            UpdateUrlEvent::class
        );

        $parameters = [
            $this->createMock(URL::class),
            $this->createMock(URLUpdateStruct::class),
        ];

        $updatedUrl = $this->createMock(URL::class);
        $innerServiceMock = $this->createMock(URLServiceInterface::class);
        $innerServiceMock->method('updateUrl')->willReturn($updatedUrl);

        $service = new URLService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateUrl(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedUrl, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateUrlEvent::class, 0],
            [UpdateUrlEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdateUrlResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateUrlEvent::class,
            UpdateUrlEvent::class
        );

        $parameters = [
            $this->createMock(URL::class),
            $this->createMock(URLUpdateStruct::class),
        ];

        $updatedUrl = $this->createMock(URL::class);
        $eventUpdatedUrl = $this->createMock(URL::class);
        $innerServiceMock = $this->createMock(URLServiceInterface::class);
        $innerServiceMock->method('updateUrl')->willReturn($updatedUrl);

        $traceableEventDispatcher->addListener(BeforeUpdateUrlEvent::class, static function (BeforeUpdateUrlEvent $event) use ($eventUpdatedUrl) {
            $event->setUpdatedUrl($eventUpdatedUrl);
        }, 10);

        $service = new URLService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateUrl(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedUrl, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateUrlEvent::class, 10],
            [BeforeUpdateUrlEvent::class, 0],
            [UpdateUrlEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateUrlStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateUrlEvent::class,
            UpdateUrlEvent::class
        );

        $parameters = [
            $this->createMock(URL::class),
            $this->createMock(URLUpdateStruct::class),
        ];

        $updatedUrl = $this->createMock(URL::class);
        $eventUpdatedUrl = $this->createMock(URL::class);
        $innerServiceMock = $this->createMock(URLServiceInterface::class);
        $innerServiceMock->method('updateUrl')->willReturn($updatedUrl);

        $traceableEventDispatcher->addListener(BeforeUpdateUrlEvent::class, static function (BeforeUpdateUrlEvent $event) use ($eventUpdatedUrl) {
            $event->setUpdatedUrl($eventUpdatedUrl);
            $event->stopPropagation();
        }, 10);

        $service = new URLService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateUrl(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedUrl, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateUrlEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUpdateUrlEvent::class, 0],
            [UpdateUrlEvent::class, 0],
        ]);
    }
}

class_alias(URLServiceTest::class, 'eZ\Publish\Core\Event\Tests\URLServiceTest');
