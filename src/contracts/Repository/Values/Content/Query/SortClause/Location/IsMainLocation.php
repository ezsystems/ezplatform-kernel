<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location;

/**
 * Sets sort direction on the Location main status for a Location query.
 */
class IsMainLocation extends Location
{
    /**
     * Constructs a new Location IsMainLocation SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct(string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('location_is_main', $sortDirection);
    }
}

class_alias(IsMainLocation::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\IsMainLocation');
