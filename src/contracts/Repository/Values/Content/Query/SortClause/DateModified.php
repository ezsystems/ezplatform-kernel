<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;

/**
 * Sets sort direction on the content modification date for a content query.
 */
class DateModified extends SortClause implements FilteringSortClause
{
    /**
     * Constructs a new DateModified SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct(string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('date_modified', $sortDirection);
    }
}

class_alias(DateModified::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\DateModified');
