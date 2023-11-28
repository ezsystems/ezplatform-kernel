<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\SharedGateway\DatabasePlatform;

use eZ\Publish\Core\Base\Exceptions\DatabaseException;
use eZ\Publish\Core\Persistence\Legacy\SharedGateway\Gateway;

final class SqliteGateway implements Gateway
{
    /**
     * Error code 7 for a fatal error - taken from an existing driver implementation.
     */
    private const FATAL_ERROR_CODE = 7;

    /** @var array<string, int> */
    private $lastInsertedIds = [];

    public function getColumnNextIntegerValue(
        string $tableName,
        string $columnName,
        string $sequenceName
    ): ?int {
        $lastId = $this->lastInsertedIds[$sequenceName] ?? 0;
        $nextId = (int)hrtime(true);

        // $lastId === $nextId shouldn't happen using high-resolution time, but better safe than sorry
        return $this->lastInsertedIds[$sequenceName] = $lastId === $nextId ? $nextId + 1 : $nextId;
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\DatabaseException if the sequence has no last value
     */
    public function getLastInsertedId(string $sequenceName): int
    {
        if (!isset($this->lastInsertedIds[$sequenceName])) {
            throw new DatabaseException(
                "Sequence '{$sequenceName}' is not yet defined in this session",
                self::FATAL_ERROR_CODE
            );
        }

        return $this->lastInsertedIds[$sequenceName];
    }
}
