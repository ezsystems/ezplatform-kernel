<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;

interface SortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool;

    public function buildQuery(
        FilteringQueryBuilder $queryBuilder,
        FilteringSortClause $sortClause
    ): void;
}

class_alias(SortClauseQueryBuilder::class, 'eZ\Publish\SPI\Repository\Values\Filter\SortClauseQueryBuilder');
