<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;
use Ibexa\Contracts\Core\Repository\Values\Trash\Query\SortClause as TrashSortClause;

/**
 * Sets sort direction on the Location path for a Location query.
 */
class Path extends Location implements FilteringSortClause, TrashSortClause
{
    /**
     * Constructs a new LocationPath SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct(string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('location_path', $sortDirection);
    }
}

class_alias(Path::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path');
