<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Gateway;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Persistence\Content\UrlWildcard;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Gateway;
use PDOException;

/**
 * @internal Internal exception conversion layer.
 */
final class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Gateway
     */
    private $innerGateway;

    /**
     * Create a new exception conversion gateway around $innerGateway.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Gateway $innerGateway
     */
    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    public function insertUrlWildcard(UrlWildcard $urlWildcard): int
    {
        try {
            return $this->innerGateway->insertUrlWildcard($urlWildcard);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function updateUrlWildcard(
        int $id,
        string $sourceUrl,
        string $destinationUrl,
        bool $forward
    ): void {
        try {
            $this->innerGateway->updateUrlWildcard(
                $id,
                $sourceUrl,
                $destinationUrl,
                $forward
            );
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function deleteUrlWildcard(int $id): void
    {
        try {
            $this->innerGateway->deleteUrlWildcard($id);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadUrlWildcardData(int $id): array
    {
        try {
            return $this->innerGateway->loadUrlWildcardData($id);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadUrlWildcardsData(int $offset = 0, int $limit = -1): array
    {
        try {
            return $this->innerGateway->loadUrlWildcardsData($offset, $limit);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    public function loadUrlWildcardBySourceUrl(string $sourceUrl): array
    {
        try {
            return $this->innerGateway->loadUrlWildcardBySourceUrl($sourceUrl);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}

class_alias(ExceptionConversion::class, 'eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Gateway\ExceptionConversion');
