<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Gateway;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Search\Legacy\Content\Gateway;
use PDOException;

/**
 * The Content Search Gateway provides the implementation for one database to
 * retrieve the desired content objects.
 */
class ExceptionConversion extends Gateway
{
    /**
     * @var \Ibexa\Core\Search\Legacy\Content\Gateway
     */
    protected $innerGateway;

    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    public function find(
        Criterion $criterion,
        $offset = 0,
        $limit = null,
        array $sort = null,
        array $languageFilter = [],
        $doCount = true
    ): array {
        try {
            return $this->innerGateway->find($criterion, $offset, $limit, $sort, $languageFilter, $doCount);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}

class_alias(ExceptionConversion::class, 'eZ\Publish\Core\Search\Legacy\Content\Gateway\ExceptionConversion');
