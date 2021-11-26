<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\NotificationServiceDecorator;
use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotificationServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): NotificationService
    {
        return new class($service) extends NotificationServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(NotificationService::class);
    }

    public function testLoadNotificationsDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            264,
            959,
        ];

        $serviceMock->expects($this->once())->method('loadNotifications')->with(...$parameters);

        $decoratedService->loadNotifications(...$parameters);
    }

    public function testGetNotificationDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [469];

        $serviceMock->expects($this->once())->method('getNotification')->with(...$parameters);

        $decoratedService->getNotification(...$parameters);
    }

    public function testMarkNotificationAsReadDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Notification::class)];

        $serviceMock->expects($this->once())->method('markNotificationAsRead')->with(...$parameters);

        $decoratedService->markNotificationAsRead(...$parameters);
    }

    public function testGetPendingNotificationCountDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('getPendingNotificationCount')->with(...$parameters);

        $decoratedService->getPendingNotificationCount(...$parameters);
    }

    public function testGetNotificationCountDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('getNotificationCount')->with(...$parameters);

        $decoratedService->getNotificationCount(...$parameters);
    }

    public function testCreateNotificationDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(CreateStruct::class)];

        $serviceMock->expects($this->once())->method('createNotification')->with(...$parameters);

        $decoratedService->createNotification(...$parameters);
    }

    public function testDeleteNotificationDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Notification::class)];

        $serviceMock->expects($this->once())->method('deleteNotification')->with(...$parameters);

        $decoratedService->deleteNotification(...$parameters);
    }
}

class_alias(NotificationServiceDecoratorTest::class, 'eZ\Publish\SPI\Repository\Tests\Decorator\NotificationServiceDecoratorTest');
