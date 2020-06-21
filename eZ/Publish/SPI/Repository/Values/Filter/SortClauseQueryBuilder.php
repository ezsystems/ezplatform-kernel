<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Repository\Values\Filter;

use eZ\Publish\SPI\Persistence\Filter\Doctrine\FilteringQueryBuilder;

interface SortClauseQueryBuilder
{
    /**
     * Tag name to be used when defining Service in Dependency Injection Container.
     * Note that it's recommended to rely on auto-configuration instead.
     */
    public const SYMFONY_TAG_NAME = 'ezplatform.filter.sort_clause.query_builder';

    public function accepts(FilteringSortClause $sortClause): bool;

    public function buildQuery(
        FilteringQueryBuilder $queryBuilder,
        FilteringSortClause $sortClause
    ): void;
}
