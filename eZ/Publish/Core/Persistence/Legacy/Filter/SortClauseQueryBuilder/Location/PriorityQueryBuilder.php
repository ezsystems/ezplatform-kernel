<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location;

use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location;
use eZ\Publish\SPI\Repository\Values\Filter\FilteringSortClause;

class PriorityQueryBuilder extends BaseLocationSortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool
    {
        return $sortClause instanceof Location\Priority;
    }

    protected function getSortingExpression(): string
    {
        return 'location.priority';
    }
}
