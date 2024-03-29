<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Persistence\Filter;

use eZ\Publish\SPI\Persistence\Filter\Doctrine\FilteringQueryBuilder;

/**
 * @internal for internal use by Repository Filtering.
 * Visits instances of {@see \eZ\Publish\SPI\Repository\Values\Filter\SortClauseQueryBuilder}.
 */
interface SortClauseVisitor
{
    /**
     * @param \eZ\Publish\SPI\Repository\Values\Filter\FilteringSortClause[] $sortClauses
     */
    public function visitSortClauses(FilteringQueryBuilder $queryBuilder, array $sortClauses): void;
}
