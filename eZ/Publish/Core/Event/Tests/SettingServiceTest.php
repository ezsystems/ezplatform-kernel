<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Event\Tests;

use eZ\Publish\API\Repository\Events\Setting\BeforeCreateSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\BeforeDeleteSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\BeforeUpdateSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\CreateSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\DeleteSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\UpdateSettingEvent;
use eZ\Publish\API\Repository\SettingService as SettingServiceInterface;
use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;
use eZ\Publish\Core\Event\SettingService;

class SettingServiceTest extends AbstractServiceTest
{
    public function testUpdateSettingEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSettingEvent::class,
            UpdateSettingEvent::class
        );

        $parameters = [
            $this->createMock(Setting::class),
            $this->createMock(SettingUpdateStruct::class),
        ];

        $updatedSetting = $this->createMock(Setting::class);
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('updateSetting')->willReturn($updatedSetting);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        self::assertSame($updatedSetting, $result);
        self::assertSame($calledListeners, [
            [BeforeUpdateSettingEvent::class, 0],
            [UpdateSettingEvent::class, 0],
        ]);
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdateSettingResultInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSettingEvent::class,
            UpdateSettingEvent::class
        );

        $parameters = [
            $this->createMock(Setting::class),
            $this->createMock(SettingUpdateStruct::class),
        ];

        $updatedSetting = $this->createMock(Setting::class);
        $eventUpdatedSetting = $this->createMock(Setting::class);
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('updateSetting')->willReturn($updatedSetting);

        $traceableEventDispatcher->addListener(BeforeUpdateSettingEvent::class, function (BeforeUpdateSettingEvent $event) use ($eventUpdatedSetting) {
            $event->setUpdatedSetting($eventUpdatedSetting);
        }, 10);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        self::assertSame($eventUpdatedSetting, $result);
        self::assertSame($calledListeners, [
            [BeforeUpdateSettingEvent::class, 10],
            [BeforeUpdateSettingEvent::class, 0],
            [UpdateSettingEvent::class, 0],
        ]);
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateSettingStopPropagationInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSettingEvent::class,
            UpdateSettingEvent::class
        );

        $parameters = [
            $this->createMock(Setting::class),
            $this->createMock(SettingUpdateStruct::class),
        ];

        $updatedSetting = $this->createMock(Setting::class);
        $eventUpdatedSetting = $this->createMock(Setting::class);
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('updateSetting')->willReturn($updatedSetting);

        $traceableEventDispatcher->addListener(BeforeUpdateSettingEvent::class, function (BeforeUpdateSettingEvent $event) use ($eventUpdatedSetting) {
            $event->setUpdatedSetting($eventUpdatedSetting);
            $event->stopPropagation();
        }, 10);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        self::assertSame($eventUpdatedSetting, $result);
        self::assertSame($calledListeners, [
            [BeforeUpdateSettingEvent::class, 10],
        ]);
        self::assertSame($notCalledListeners, [
            [BeforeUpdateSettingEvent::class, 0],
            [UpdateSettingEvent::class, 0],
        ]);
    }

    public function testDeleteSettingEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteSettingEvent::class,
            DeleteSettingEvent::class
        );

        $parameters = [
            $this->createMock(Setting::class),
        ];

        $innerServiceMock = $this->createMock(SettingServiceInterface::class);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        self::assertSame($calledListeners, [
            [BeforeDeleteSettingEvent::class, 0],
            [DeleteSettingEvent::class, 0],
        ]);
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testDeleteSettingStopPropagationInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteSettingEvent::class,
            DeleteSettingEvent::class
        );

        $parameters = [
            $this->createMock(Setting::class),
        ];

        $innerServiceMock = $this->createMock(SettingServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeDeleteSettingEvent::class, function (BeforeDeleteSettingEvent $event) {
            $event->stopPropagation();
        }, 10);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        self::assertSame($calledListeners, [
            [BeforeDeleteSettingEvent::class, 10],
        ]);
        self::assertSame($notCalledListeners, [
            [BeforeDeleteSettingEvent::class, 0],
            [DeleteSettingEvent::class, 0],
        ]);
    }

    public function testCreateSettingEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSettingEvent::class,
            CreateSettingEvent::class
        );

        $parameters = [
            $this->createMock(SettingCreateStruct::class),
        ];

        $setting = $this->createMock(Setting::class);
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('createSetting')->willReturn($setting);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        self::assertSame($setting, $result);
        self::assertSame($calledListeners, [
            [BeforeCreateSettingEvent::class, 0],
            [CreateSettingEvent::class, 0],
        ]);
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnCreateSettingResultInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSettingEvent::class,
            CreateSettingEvent::class
        );

        $parameters = [
            $this->createMock(SettingCreateStruct::class),
        ];

        $setting = $this->createMock(Setting::class);
        $eventSetting = $this->createMock(Setting::class);
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('createSetting')->willReturn($setting);

        $traceableEventDispatcher->addListener(BeforeCreateSettingEvent::class, function (BeforeCreateSettingEvent $event) use ($eventSetting) {
            $event->setSetting($eventSetting);
        }, 10);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        self::assertSame($eventSetting, $result);
        self::assertSame($calledListeners, [
            [BeforeCreateSettingEvent::class, 10],
            [BeforeCreateSettingEvent::class, 0],
            [CreateSettingEvent::class, 0],
        ]);
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testCreateSettingStopPropagationInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSettingEvent::class,
            CreateSettingEvent::class
        );

        $parameters = [
            $this->createMock(SettingCreateStruct::class),
        ];

        $setting = $this->createMock(Setting::class);
        $eventSetting = $this->createMock(Setting::class);
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('createSetting')->willReturn($setting);

        $traceableEventDispatcher->addListener(BeforeCreateSettingEvent::class, function (BeforeCreateSettingEvent $event) use ($eventSetting) {
            $event->setSetting($eventSetting);
            $event->stopPropagation();
        }, 10);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createSetting(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        self::assertSame($eventSetting, $result);
        self::assertSame($calledListeners, [
            [BeforeCreateSettingEvent::class, 10],
        ]);
        self::assertSame($notCalledListeners, [
            [BeforeCreateSettingEvent::class, 0],
            [CreateSettingEvent::class, 0],
        ]);
    }
}
