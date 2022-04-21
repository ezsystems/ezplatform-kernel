<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\SortClause;

class Id extends SortClause
{
    /**
     * Constructs a new Id SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct(string $sortDirection = self::SORT_ASC)
    {
        parent::__construct('id', $sortDirection);
    }
}
