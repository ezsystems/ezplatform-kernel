<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\User\Gateway;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Persistence\User;
use Ibexa\Contracts\Core\Persistence\User\UserTokenUpdateStruct;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Persistence\Legacy\User\Gateway;
use PDOException;

/**
 * @internal Internal exception conversion layer.
 */
final class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway.
     *
     * @var \Ibexa\Core\Persistence\Legacy\User\Gateway
     */
    private $innerGateway;

    /**
     * Create a new exception conversion gateway around $innerGateway.
     *
     * @param \Ibexa\Core\Persistence\Legacy\User\Gateway $innerGateway
     */
    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    public function load(int $userId): array
    {
        try {
            return $this->innerGateway->load($userId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadByLogin(string $login): array
    {
        try {
            return $this->innerGateway->loadByLogin($login);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadByEmail(string $email): array
    {
        try {
            return $this->innerGateway->loadByEmail($email);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadUserByToken(string $hash): array
    {
        try {
            return $this->innerGateway->loadUserByToken($hash);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function updateUserPassword(User $user): void
    {
        try {
            $this->innerGateway->updateUserPassword($user);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function updateUserToken(UserTokenUpdateStruct $userTokenUpdateStruct): void
    {
        try {
            $this->innerGateway->updateUserToken($userTokenUpdateStruct);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function expireUserToken(string $hash): void
    {
        try {
            $this->innerGateway->expireUserToken($hash);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function assignRole(int $contentId, int $roleId, array $limitation): void
    {
        try {
            $this->innerGateway->assignRole($contentId, $roleId, $limitation);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function removeRole(int $contentId, int $roleId): void
    {
        try {
            $this->innerGateway->removeRole($contentId, $roleId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function removeRoleAssignmentById(int $roleAssignmentId): void
    {
        try {
            $this->innerGateway->removeRoleAssignmentById($roleAssignmentId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}

class_alias(ExceptionConversion::class, 'eZ\Publish\Core\Persistence\Legacy\User\Gateway\ExceptionConversion');
