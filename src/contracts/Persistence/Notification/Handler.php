<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Notification;

use Ibexa\Contracts\Core\Repository\Values\Notification\Notification as APINotification;

interface Handler
{
    /**
     * Store Notification ValueObject in persistent storage.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Notification\CreateStruct $createStruct
     *
     * @return \Ibexa\Contracts\Core\Persistence\Notification\Notification
     */
    public function createNotification(CreateStruct $createStruct): Notification;

    /**
     * Update Notification ValueObject in persistent storage.
     * There's no edit feature but it's essential to mark Notification as read.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     * @param \Ibexa\Contracts\Core\Persistence\Notification\UpdateStruct $updateStruct
     *
     * @return \Ibexa\Contracts\Core\Persistence\Notification\Notification
     */
    public function updateNotification(APINotification $notification, UpdateStruct $updateStruct): Notification;

    /**
     * Count users unread Notifications.
     *
     * @param int $ownerId
     *
     * @return int
     */
    public function countPendingNotifications(int $ownerId): int;

    /**
     * Get Notification by its id.
     *
     * @param int $notificationId
     *
     * @return \Ibexa\Contracts\Core\Persistence\Notification\Notification
     */
    public function getNotificationById(int $notificationId): Notification;

    /**
     * @param int $userId
     * @param int $offset
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Core\Persistence\Notification\Notification[]
     */
    public function loadUserNotifications(int $userId, int $offset, int $limit): array;

    /**
     * @param int $currentUserId
     *
     * @return int
     */
    public function countNotifications(int $currentUserId): int;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     */
    public function delete(APINotification $notification): void;
}

class_alias(Handler::class, 'eZ\Publish\SPI\Persistence\Notification\Handler');
