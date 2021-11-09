<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\SharedGateway\DatabasePlatform;

use Doctrine\DBAL\Connection;
use Ibexa\Core\Persistence\Legacy\SharedGateway\Gateway;

final class FallbackGateway implements Gateway
{
    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getColumnNextIntegerValue(
        string $tableName,
        string $columnName,
        string $sequenceName
    ): ?int {
        return null;
    }

    public function getLastInsertedId(string $sequenceName): int
    {
        return (int)$this->connection->lastInsertId($sequenceName);
    }
}

class_alias(FallbackGateway::class, 'eZ\Publish\Core\Persistence\Legacy\SharedGateway\DatabasePlatform\FallbackGateway');
