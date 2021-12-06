<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\NotificationService as NotificationServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Contracts\Core\Repository\Values\Notification\NotificationList;

class NotificationService implements NotificationServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    protected $service;

    /**
     * Construct service object from aggregated service.
     *
     * @param \Ibexa\Contracts\Core\Repository\NotificationService $service
     */
    public function __construct(
        NotificationServiceInterface $service
    ) {
        $this->service = $service;
    }

    /**
     * Get currently logged user notifications.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Notification\NotificationList
     */
    public function loadNotifications(int $offset, int $limit): NotificationList
    {
        return $this->service->loadNotifications($offset, $limit);
    }

    /**
     * @param int $notificationId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Notification\Notification
     */
    public function getNotification(int $notificationId): Notification
    {
        return $this->service->getNotification($notificationId);
    }

    /**
     * Mark notification as read so it no longer bother the user.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     */
    public function markNotificationAsRead(Notification $notification): void
    {
        $this->service->markNotificationAsRead($notification);
    }

    /**
     * Get count of unread users notifications.
     *
     * @return int
     */
    public function getPendingNotificationCount(): int
    {
        return $this->service->getPendingNotificationCount();
    }

    /**
     * Get count of total users notifications.
     *
     * @return int
     */
    public function getNotificationCount(): int
    {
        return $this->service->getNotificationCount();
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     */
    public function deleteNotification(Notification $notification): void
    {
        $this->service->deleteNotification($notification);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct $createStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Notification\Notification
     */
    public function createNotification(CreateStruct $createStruct): Notification
    {
        return $this->service->createNotification($createStruct);
    }
}

class_alias(NotificationService::class, 'eZ\Publish\Core\Repository\SiteAccessAware\NotificationService');
