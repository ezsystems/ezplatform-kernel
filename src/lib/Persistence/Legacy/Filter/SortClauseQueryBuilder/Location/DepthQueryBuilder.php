<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;

/**
 * @internal
 */
final class DepthQueryBuilder extends BaseLocationSortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool
    {
        return $sortClause instanceof Location\Depth;
    }

    protected function getSortingExpression(): string
    {
        return 'location.depth';
    }
}

class_alias(DepthQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location\DepthQueryBuilder');
