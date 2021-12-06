<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;
use Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder;

/**
 * @internal
 */
abstract class BaseLocationSortClauseQueryBuilder implements SortClauseQueryBuilder
{
    public function buildQuery(
        FilteringQueryBuilder $queryBuilder,
        FilteringSortClause $sortClause
    ): void {
        $sort = $this->getSortingExpression();
        $queryBuilder
            ->addSelect($this->getSortingExpression())
            ->joinAllLocations();

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause $sortClause */
        $queryBuilder->addOrderBy($sort, $sortClause->direction);
    }

    abstract protected function getSortingExpression(): string;
}

class_alias(BaseLocationSortClauseQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location\BaseLocationSortClauseQueryBuilder');
