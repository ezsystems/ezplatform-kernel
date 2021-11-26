<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Persistence\Filter\SortClauseVisitor as FilteringSortClauseVisitor;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;
use Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder;

/**
 * @internal Type-hint {@see \Ibexa\Contracts\Core\Persistence\Filter\SortClauseVisitor} instead.
 */
final class SortClauseVisitor implements FilteringSortClauseVisitor
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder[] */
    private $sortClauseQueryBuilders;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder[] */
    private static $queryBuildersForSortClauses = [];

    public function __construct(iterable $sortClauseQueryBuilders)
    {
        $this->sortClauseQueryBuilders = $sortClauseQueryBuilders;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause[] $sortClauses
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException if there's no builder for a Sort Clause
     */
    public function visitSortClauses(FilteringQueryBuilder $queryBuilder, array $sortClauses): void
    {
        foreach ($sortClauses as $sortClause) {
            $this
                ->getQueryBuilderForSortClause($sortClause)
                ->buildQuery($queryBuilder, $sortClause);
        }
    }

    /**
     * Cache Query Builders in-memory and get the one for the given Sort Clause.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    private function getQueryBuilderForSortClause(
        FilteringSortClause $sortClause
    ): SortClauseQueryBuilder {
        $sortClauseFQCN = get_class($sortClause);
        if (!isset(self::$queryBuildersForSortClauses[$sortClauseFQCN])) {
            foreach ($this->sortClauseQueryBuilders as $sortClauseQueryBuilder) {
                if ($sortClauseQueryBuilder->accepts($sortClause)) {
                    self::$queryBuildersForSortClauses[$sortClauseFQCN] = $sortClauseQueryBuilder;
                    break;
                }
            }
        }

        if (!isset(self::$queryBuildersForSortClauses[$sortClauseFQCN])) {
            throw new NotImplementedException(
                "There are no Query Builders for {$sortClauseFQCN} Sort Clause"
            );
        }

        return self::$queryBuildersForSortClauses[$sortClauseFQCN];
    }
}

class_alias(SortClauseVisitor::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\SortClauseVisitor');
