<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * The Content Search Gateway provides the implementation for one database to
 * retrieve the desired content objects.
 */
abstract class Gateway
{
    /**
     * Returns a list of object satisfying the $criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     * @param int $offset
     * @param int $limit
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sort
     * @param array $languageFilter
     * @param bool $doCount
     *
     * @return mixed[][]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if Criterion is not applicable to its target
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException if a given Criterion Handler or Sort Clause is not implemented
     */
    abstract public function find(
        Criterion $criterion,
        $offset,
        $limit,
        array $sort = null,
        array $languageFilter = [],
        $doCount = true
    ): array;
}

class_alias(Gateway::class, 'eZ\Publish\Core\Search\Legacy\Content\Gateway');
