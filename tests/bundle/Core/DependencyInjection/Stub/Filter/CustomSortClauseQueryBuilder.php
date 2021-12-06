<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Stub\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;
use Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder;

/**
 * Stub for {@see \Ibexa\Tests\Bundle\Core\DependencyInjection\IbexaCoreExtensionTest::testFilteringQueryBuildersAutomaticConfiguration}.
 */
class CustomSortClauseQueryBuilder implements SortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool
    {
        return true;
    }

    public function buildQuery(
        FilteringQueryBuilder $queryBuilder,
        FilteringSortClause $sortClause
    ): void {
        // Do nothing
    }
}

class_alias(CustomSortClauseQueryBuilder::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Stub\Filter\CustomSortClauseQueryBuilder');
