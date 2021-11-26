<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\URL;

use Ibexa\Contracts\Core\Persistence\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;

abstract class Gateway
{
    /**
     * Update the URL.
     *
     * @param \Ibexa\Contracts\Core\Persistence\URL\URL $url
     */
    abstract public function updateUrl(URL $url);

    /**
     * Selects URLs matching specified criteria.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion $criterion
     * @param int $offset
     * @param int $limit
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\Query\SortClause[] $sortClauses
     * @param bool $doCount
     *
     * @return array{
     *     "rows": mixed,
     *     "count": int|null,
     * }
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException if Criterion is not applicable to its target
     */
    abstract public function find(Criterion $criterion, $offset, $limit, array $sortClauses = [], $doCount = true);

    /**
     * Returns IDs of Content Objects using URL identified by $id.
     *
     * @param int $id
     *
     * @return array
     */
    abstract public function findUsages($id);

    /**
     * Loads URL with url id.
     *
     * @param int $id
     *
     * @return array
     */
    abstract public function loadUrlData($id);

    /**
     * Loads URL with url address.
     *
     * @param int $url
     *
     * @return array
     */
    abstract public function loadUrlDataByUrl($url);
}

class_alias(Gateway::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Gateway');
