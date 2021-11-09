<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;

class VisibilityQueryBuilder extends BaseLocationSortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool
    {
        return $sortClause instanceof Location\Visibility;
    }

    protected function getSortingExpression(): string
    {
        return 'location.is_invisible';
    }
}

class_alias(VisibilityQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location\VisibilityQueryBuilder');
