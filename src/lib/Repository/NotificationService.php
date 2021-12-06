<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use DateTime;
use Ibexa\Contracts\Core\Persistence\Notification\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Notification\Handler;
use Ibexa\Contracts\Core\Persistence\Notification\Notification;
use Ibexa\Contracts\Core\Persistence\Notification\UpdateStruct;
use Ibexa\Contracts\Core\Repository\NotificationService as NotificationServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct as APICreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification as APINotification;
use Ibexa\Contracts\Core\Repository\Values\Notification\NotificationList;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;

class NotificationService implements NotificationServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Persistence\Notification\Handler */
    protected $persistenceHandler;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Notification\Handler $persistenceHandler
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(Handler $persistenceHandler, PermissionResolver $permissionResolver)
    {
        $this->persistenceHandler = $persistenceHandler;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function loadNotifications(int $offset = 0, int $limit = 25): NotificationList
    {
        $currentUserId = $this->getCurrentUserId();

        $list = new NotificationList();
        $list->totalCount = $this->persistenceHandler->countNotifications($currentUserId);
        if ($list->totalCount > 0) {
            $list->items = array_map(function (Notification $spiNotification) {
                return $this->buildDomainObject($spiNotification);
            }, $this->persistenceHandler->loadUserNotifications($currentUserId, $offset, $limit));
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function createNotification(APICreateStruct $createStruct): APINotification
    {
        $spiCreateStruct = new CreateStruct();

        if (empty($createStruct->ownerId)) {
            throw new InvalidArgumentException('ownerId', $createStruct->ownerId);
        }

        $spiCreateStruct->ownerId = $createStruct->ownerId;

        if (empty($createStruct->type)) {
            throw new InvalidArgumentException('type', $createStruct->type);
        }

        $spiCreateStruct->type = $createStruct->type;
        $spiCreateStruct->isPending = (bool) $createStruct->isPending;
        $spiCreateStruct->data = $createStruct->data;
        $spiCreateStruct->created = (new DateTime())->getTimestamp();

        return $this->buildDomainObject(
            $this->persistenceHandler->createNotification($spiCreateStruct)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification(int $notificationId): APINotification
    {
        $notification = $this->persistenceHandler->getNotificationById($notificationId);

        $currentUserId = $this->getCurrentUserId();
        if (!$notification->ownerId || $currentUserId != $notification->ownerId) {
            throw new NotFoundException('Notification', $notificationId);
        }

        return $this->buildDomainObject($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function markNotificationAsRead(APINotification $notification): void
    {
        $currentUserId = $this->getCurrentUserId();

        if (!$notification->id) {
            throw new NotFoundException('Notification', $notification->id);
        }

        if ($notification->ownerId !== $currentUserId) {
            throw new UnauthorizedException($notification->id, 'Notification');
        }

        if (!$notification->isPending) {
            return;
        }

        $updateStruct = new UpdateStruct();
        $updateStruct->isPending = false;

        $this->persistenceHandler->updateNotification($notification, $updateStruct);
    }

    /**
     * {@inheritdoc}
     */
    public function getPendingNotificationCount(): int
    {
        return $this->persistenceHandler->countPendingNotifications(
            $this->getCurrentUserId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationCount(): int
    {
        return $this->persistenceHandler->countNotifications(
            $this->getCurrentUserId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteNotification(APINotification $notification): void
    {
        $this->persistenceHandler->delete($notification);
    }

    /**
     * Builds Notification domain object from ValueObject returned by Persistence API.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Notification\Notification $spiNotification
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Notification\Notification
     */
    protected function buildDomainObject(Notification $spiNotification): APINotification
    {
        return new APINotification([
            'id' => $spiNotification->id,
            'ownerId' => $spiNotification->ownerId,
            'isPending' => $spiNotification->isPending,
            'type' => $spiNotification->type,
            'created' => new DateTime("@{$spiNotification->created}"),
            'data' => $spiNotification->data,
        ]);
    }

    private function getCurrentUserId(): int
    {
        return $this->permissionResolver
            ->getCurrentUserReference()
            ->getUserId();
    }
}

class_alias(NotificationService::class, 'eZ\Publish\Core\Repository\NotificationService');
