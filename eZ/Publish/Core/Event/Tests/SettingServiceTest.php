<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;

class SettingServiceTest extends AbstractServiceTest
{
    public function testUpdateSettingEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSettingEvent::class,
            UpdateSettingEvent::class
        );
        $updatedSetting = $this->createMock(Setting::class);

        $result = $this->updateSetting($updatedSetting, $traceableEventDispatcher);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );

        self::assertSame($updatedSetting, $result);
        self::assertSame(
            $calledListeners,
            [
                [BeforeUpdateSettingEvent::class, 0],
                [UpdateSettingEvent::class, 0],
            ]
        );
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdateSettingResultInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSettingEvent::class,
            UpdateSettingEvent::class
        );
        $eventUpdatedSetting = $this->createMock(Setting::class);

        $traceableEventDispatcher->addListener(
            BeforeUpdateSettingEvent::class,
            static function (BeforeUpdateSettingEvent $event) use ($eventUpdatedSetting) {
                $event->setUpdatedSetting($eventUpdatedSetting);
            },
            10
        );
        $updatedSetting = $this->createMock(Setting::class);

        $result = $this->updateSetting($updatedSetting, $traceableEventDispatcher);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );

        self::assertSame($eventUpdatedSetting, $result);
        self::assertSame(
            $calledListeners,
            [
                [BeforeUpdateSettingEvent::class, 10],
                [BeforeUpdateSettingEvent::class, 0],
                [UpdateSettingEvent::class, 0],
            ]
        );
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateSettingStopPropagationInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSettingEvent::class,
            UpdateSettingEvent::class
        );
        $eventUpdatedSetting = $this->createMock(Setting::class);
        $traceableEventDispatcher->addListener(
            BeforeUpdateSettingEvent::class,
            static function (BeforeUpdateSettingEvent $event) use ($eventUpdatedSetting) {
                $event->setUpdatedSetting($eventUpdatedSetting);
                $event->stopPropagation();
            },
            10
        );
        $updatedSetting = $this->createMock(Setting::class);

        $result = $this->updateSetting($updatedSetting, $traceableEventDispatcher);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );
        $notCalledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getNotCalledListeners()
        );

        self::assertSame($eventUpdatedSetting, $result);
        self::assertSame(
            $calledListeners,
            [
                [BeforeUpdateSettingEvent::class, 10],
            ]
        );
        self::assertSame(
            $notCalledListeners,
            [
                [BeforeUpdateSettingEvent::class, 0],
                [UpdateSettingEvent::class, 0],
            ]
        );
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

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );

        self::assertSame(
            $calledListeners,
            [
                [BeforeDeleteSettingEvent::class, 0],
                [DeleteSettingEvent::class, 0],
            ]
        );
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

        $traceableEventDispatcher->addListener(
            BeforeDeleteSettingEvent::class,
            static function (BeforeDeleteSettingEvent $event) {
                $event->stopPropagation();
            },
            10
        );

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteSetting(...$parameters);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );
        $notCalledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getNotCalledListeners()
        );

        self::assertSame(
            $calledListeners,
            [
                [BeforeDeleteSettingEvent::class, 10],
            ]
        );
        self::assertSame(
            $notCalledListeners,
            [
                [BeforeDeleteSettingEvent::class, 0],
                [DeleteSettingEvent::class, 0],
            ]
        );
    }

    public function testCreateSettingEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSettingEvent::class,
            CreateSettingEvent::class
        );
        $setting = $this->createMock(Setting::class);

        $result = $this->createSetting($setting, $traceableEventDispatcher);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );

        self::assertSame($setting, $result);
        self::assertSame(
            $calledListeners,
            [
                [BeforeCreateSettingEvent::class, 0],
                [CreateSettingEvent::class, 0],
            ]
        );
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnCreateSettingResultInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSettingEvent::class,
            CreateSettingEvent::class
        );
        $eventSetting = $this->createMock(Setting::class);
        $setting = $this->createMock(Setting::class);
        $traceableEventDispatcher->addListener(
            BeforeCreateSettingEvent::class,
            static function (BeforeCreateSettingEvent $event) use ($eventSetting) {
                $event->setSetting($eventSetting);
            },
            10
        );

        $result = $this->createSetting($setting, $traceableEventDispatcher);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );

        self::assertSame($eventSetting, $result);
        self::assertSame(
            $calledListeners,
            [
                [BeforeCreateSettingEvent::class, 10],
                [BeforeCreateSettingEvent::class, 0],
                [CreateSettingEvent::class, 0],
            ]
        );
        self::assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testCreateSettingStopPropagationInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSettingEvent::class,
            CreateSettingEvent::class
        );
        $eventSetting = $this->createMock(Setting::class);
        $setting = $this->createMock(Setting::class);
        $traceableEventDispatcher->addListener(
            BeforeCreateSettingEvent::class,
            static function (BeforeCreateSettingEvent $event) use ($eventSetting) {
                $event->setSetting($eventSetting);
                $event->stopPropagation();
            },
            10
        );

        $result = $this->createSetting($setting, $traceableEventDispatcher);

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );
        $notCalledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getNotCalledListeners()
        );

        self::assertSame($eventSetting, $result);
        self::assertSame(
            $calledListeners,
            [
                [BeforeCreateSettingEvent::class, 10],
            ]
        );
        self::assertSame(
            $notCalledListeners,
            [
                [BeforeCreateSettingEvent::class, 0],
                [CreateSettingEvent::class, 0],
            ]
        );
    }

    private function createSetting(
        Setting $setting,
        TraceableEventDispatcher $traceableEventDispatcher
    ): Setting {
        $parameters = [
            $this->createMock(SettingCreateStruct::class),
        ];
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('createSetting')->willReturn($setting);
        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);

        return $service->createSetting(...$parameters);
    }

    private function updateSetting(
        Setting $updatedSetting,
        TraceableEventDispatcher $traceableEventDispatcher
    ): Setting {
        $parameters = [
            $this->createMock(Setting::class),
            $this->createMock(SettingUpdateStruct::class),
        ];
        $innerServiceMock = $this->createMock(SettingServiceInterface::class);
        $innerServiceMock->method('updateSetting')->willReturn($updatedSetting);

        $service = new SettingService($innerServiceMock, $traceableEventDispatcher);

        return $service->updateSetting(...$parameters);
    }
}
