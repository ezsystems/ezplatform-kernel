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
final class IdQueryBuilder extends BaseLocationSortClauseQueryBuilder
{
    public function accepts(FilteringSortClause $sortClause): bool
    {
        return $sortClause instanceof Location\Id;
    }

    protected function getSortingExpression(): string
    {
        return 'location.node_id';
    }
}

class_alias(IdQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\SortClauseQueryBuilder\Location\IdQueryBuilder');
