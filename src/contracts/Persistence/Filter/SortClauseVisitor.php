<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;

/**
 * @internal for internal use by Repository Filtering.
 * Visits instances of {@see \Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder}.
 */
interface SortClauseVisitor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause[] $sortClauses
     */
    public function visitSortClauses(FilteringQueryBuilder $queryBuilder, array $sortClauses): void;
}

class_alias(SortClauseVisitor::class, 'eZ\Publish\SPI\Persistence\Filter\SortClauseVisitor');
