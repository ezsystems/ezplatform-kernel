<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target\RandomTarget;

/**
 * Sets sort random on a content query.
 */
class Random extends SortClause
{
    /**
     * @param int|null $seed as this depends on storage implementation.
     */
    public function __construct(?int $seed = null, string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('random', $sortDirection, new RandomTarget($seed));
    }
}

class_alias(Random::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\Random');
