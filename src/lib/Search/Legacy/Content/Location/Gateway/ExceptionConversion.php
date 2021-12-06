<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Location\Gateway;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Search\Legacy\Content\Location\Gateway;
use PDOException;

/**
 * Base class for location gateways.
 */
class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Location\Gateway
     */
    protected $innerGateway;

    /**
     * Creates a new exception conversion gateway around $innerGateway.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Location\Gateway $innerGateway
     */
    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    public function find(
        Criterion $criterion,
        $offset = 0,
        $limit = null,
        array $sortClauses = null,
        array $languageFilter = [],
        $doCount = true
    ): array {
        try {
            return $this->innerGateway->find($criterion, $offset, $limit, $sortClauses, $languageFilter, $doCount);
        } catch (DBALException | PDOException $e) {
            throw DatabaseException::wrap($e);
        }
    }
}

class_alias(ExceptionConversion::class, 'eZ\Publish\Core\Search\Legacy\Content\Location\Gateway\ExceptionConversion');
