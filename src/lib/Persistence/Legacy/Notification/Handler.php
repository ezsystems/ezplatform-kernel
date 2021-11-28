<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Notification;

use Ibexa\Contracts\Core\Persistence\Notification\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Notification\Handler as HandlerInterface;
use Ibexa\Contracts\Core\Persistence\Notification\Notification;
use Ibexa\Contracts\Core\Persistence\Notification\UpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification as APINotification;
use Ibexa\Core\Base\Exceptions\NotFoundException;

class Handler implements HandlerInterface
{
    /** @var \Ibexa\Core\Persistence\Legacy\Notification\Gateway */
    protected $gateway;

    /** @var \Ibexa\Core\Persistence\Legacy\Notification\Mapper */
    protected $mapper;

    /**
     * @param \Ibexa\Core\Persistence\Legacy\Notification\Gateway $gateway
     * @param \Ibexa\Core\Persistence\Legacy\Notification\Mapper $mapper
     */
    public function __construct(Gateway $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function createNotification(CreateStruct $createStruct): Notification
    {
        $id = $this->gateway->insert($createStruct);

        return $this->getNotificationById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function countPendingNotifications(int $ownerId): int
    {
        return $this->gateway->countUserPendingNotifications($ownerId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getNotificationById(int $notificationId): Notification
    {
        $notification = $this->mapper->extractNotificationsFromRows(
            $this->gateway->getNotificationById($notificationId)
        );

        if (count($notification) < 1) {
            throw new NotFoundException('Notification', $notificationId);
        }

        return reset($notification);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function updateNotification(APINotification $apiNotification, UpdateStruct $updateStruct): Notification
    {
        $notification = $this->mapper->createNotificationFromUpdateStruct(
            $updateStruct
        );
        $notification->id = $apiNotification->id;

        $this->gateway->updateNotification($notification);

        return $this->getNotificationById($notification->id);
    }

    /**
     * {@inheritdoc}
     */
    public function countNotifications(int $userId): int
    {
        return $this->gateway->countUserNotifications($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserNotifications(int $userId, int $offset, int $limit): array
    {
        return $this->mapper->extractNotificationsFromRows(
            $this->gateway->loadUserNotifications($userId, $offset, $limit)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(APINotification $notification): void
    {
        $this->gateway->delete($notification->id);
    }
}

class_alias(Handler::class, 'eZ\Publish\Core\Persistence\Legacy\Notification\Handler');
