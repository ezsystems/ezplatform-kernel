<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Event\Tests;

use eZ\Publish\API\Repository\Events\UserPreference\BeforeSetUserPreferenceEvent;
use eZ\Publish\API\Repository\Events\UserPreference\SetUserPreferenceEvent;
use eZ\Publish\API\Repository\UserPreferenceService as UserPreferenceServiceInterface;
use eZ\Publish\Core\Event\UserPreferenceService;

class UserPreferenceServiceTest extends AbstractServiceTest
{
    public function testSetUserPreferenceEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeSetUserPreferenceEvent::class,
            SetUserPreferenceEvent::class
        );

        $parameters = [
            [],
        ];

        $innerServiceMock = $this->createMock(UserPreferenceServiceInterface::class);

        $service = new UserPreferenceService($innerServiceMock, $traceableEventDispatcher);
        $service->setUserPreference(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeSetUserPreferenceEvent::class, 0],
            [SetUserPreferenceEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testSetUserPreferenceStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeSetUserPreferenceEvent::class,
            SetUserPreferenceEvent::class
        );

        $parameters = [
            [],
        ];

        $innerServiceMock = $this->createMock(UserPreferenceServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeSetUserPreferenceEvent::class, static function (BeforeSetUserPreferenceEvent $event) {
            $event->stopPropagation();
        }, 10);

        $service = new UserPreferenceService($innerServiceMock, $traceableEventDispatcher);
        $service->setUserPreference(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeSetUserPreferenceEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeSetUserPreferenceEvent::class, 0],
            [SetUserPreferenceEvent::class, 0],
        ]);
    }
}
