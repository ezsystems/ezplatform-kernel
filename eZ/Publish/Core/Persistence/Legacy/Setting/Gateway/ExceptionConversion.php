<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Setting\Gateway;

use Doctrine\DBAL\DBALException;
use eZ\Publish\Core\Base\Exceptions\DatabaseException;
use eZ\Publish\Core\Persistence\Legacy\Setting\Gateway;
use PDOException;

/**
 * @internal Internal exception conversion layer.
 */
final class ExceptionConversion extends Gateway
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway */
    private $innerGateway;

    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    /**
     * @throw \eZ\Publish\Core\Base\Exceptions\DatabaseException
     */
    public function insertSetting(string $group, string $identifier, string $serializedValue): int
    {
        try {
            return $this->innerGateway->insertSetting($group, $identifier, $serializedValue);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    /**
     * @throw \eZ\Publish\Core\Base\Exceptions\DatabaseException
     */
    public function updateSetting(string $group, string $identifier, string $serializedValue): void
    {
        try {
            $this->innerGateway->updateSetting($group, $identifier, $serializedValue);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    /**
     * @throw \eZ\Publish\Core\Base\Exceptions\DatabaseException
     */
    public function loadSetting(string $group, string $identifier): ?array
    {
        try {
            return $this->innerGateway->loadSetting($group, $identifier);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    /**
     * @throw \eZ\Publish\Core\Base\Exceptions\DatabaseException
     */
    public function loadSettingById(int $id): ?array
    {
        try {
            return $this->innerGateway->loadSettingById($id);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    /**
     * @throw \eZ\Publish\Core\Base\Exceptions\DatabaseException
     */
    public function deleteSetting(string $group, string $identifier): void
    {
        try {
            $this->innerGateway->deleteSetting($group, $identifier);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}
