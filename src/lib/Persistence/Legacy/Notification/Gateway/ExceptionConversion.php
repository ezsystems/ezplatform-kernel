<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Notification\Gateway;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Persistence\Notification\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Notification\Notification;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Persistence\Legacy\Notification\Gateway;
use PDOException;

class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Notification\Gateway
     */
    protected $innerGateway;

    /**
     * ExceptionConversion constructor.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Notification\Gateway $innerGateway
     */
    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    public function getNotificationById(int $notificationId): array
    {
        try {
            return $this->innerGateway->getNotificationById($notificationId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function updateNotification(Notification $notification): void
    {
        try {
            $this->innerGateway->updateNotification($notification);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function countUserNotifications(int $userId): int
    {
        try {
            return $this->innerGateway->countUserNotifications($userId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function countUserPendingNotifications(int $userId): int
    {
        try {
            return $this->innerGateway->countUserPendingNotifications($userId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadUserNotifications(int $userId, int $offset = 0, int $limit = -1): array
    {
        try {
            return $this->innerGateway->loadUserNotifications($userId, $offset, $limit);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function insert(CreateStruct $notification): int
    {
        try {
            return $this->innerGateway->insert($notification);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function delete(int $notificationId): void
    {
        try {
            $this->innerGateway->delete($notificationId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}

class_alias(ExceptionConversion::class, 'eZ\Publish\Core\Persistence\Legacy\Notification\Gateway\ExceptionConversion');
