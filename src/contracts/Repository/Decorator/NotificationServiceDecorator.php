<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Contracts\Core\Repository\Values\Notification\NotificationList;

abstract class NotificationServiceDecorator implements NotificationService
{
    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    protected $innerService;

    public function __construct(NotificationService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function loadNotifications(
        int $offset,
        int $limit
    ): NotificationList {
        return $this->innerService->loadNotifications($offset, $limit);
    }

    public function getNotification(int $notificationId): Notification
    {
        return $this->innerService->getNotification($notificationId);
    }

    public function markNotificationAsRead(Notification $notification): void
    {
        $this->innerService->markNotificationAsRead($notification);
    }

    public function getPendingNotificationCount(): int
    {
        return $this->innerService->getPendingNotificationCount();
    }

    public function getNotificationCount(): int
    {
        return $this->innerService->getNotificationCount();
    }

    public function createNotification(CreateStruct $createStruct): Notification
    {
        return $this->innerService->createNotification($createStruct);
    }

    public function deleteNotification(Notification $notification): void
    {
        $this->innerService->deleteNotification($notification);
    }
}

class_alias(NotificationServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\NotificationServiceDecorator');
