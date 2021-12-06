<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Bookmark\Gateway;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Persistence\Legacy\Bookmark\Gateway;
use PDOException;

class ExceptionConversion extends Gateway
{
    /**
     * @var \Ibexa\Core\Persistence\Legacy\Bookmark\Gateway
     */
    protected $innerGateway;

    /**
     * @param \Ibexa\Core\Persistence\Legacy\Bookmark\Gateway $innerGateway
     */
    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    public function insertBookmark(Bookmark $bookmark): int
    {
        try {
            return $this->innerGateway->insertBookmark($bookmark);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function deleteBookmark(int $id): void
    {
        try {
            $this->innerGateway->deleteBookmark($id);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadBookmarkDataByUserIdAndLocationId(int $userId, array $locationId): array
    {
        try {
            return $this->innerGateway->loadBookmarkDataByUserIdAndLocationId($userId, $locationId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadUserBookmarks(int $userId, int $offset = 0, int $limit = -1): array
    {
        try {
            return $this->innerGateway->loadUserBookmarks($userId, $offset, $limit);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function countUserBookmarks(int $userId): int
    {
        try {
            return $this->innerGateway->countUserBookmarks($userId);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function locationSwapped(int $location1Id, int $location2Id): void
    {
        try {
            $this->innerGateway->locationSwapped($location1Id, $location2Id);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}

class_alias(ExceptionConversion::class, 'eZ\Publish\Core\Persistence\Legacy\Bookmark\Gateway\ExceptionConversion');
