<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\URL;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class is used to perform a URL query.
 */
class URLQuery extends ValueObject
{
    /**
     * The Query filter.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion
     */
    public $filter;

    /**
     * Query sorting clauses.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\URL\Query\SortClause[]
     */
    public $sortClauses = [];

    /**
     * Query offset.
     *
     * Sets the offset for search hits, used for paging the results.
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Query limit.
     *
     * Limit for number of search hits to return.
     * If value is `0`, search query will not return any search hits, useful for doing a count.
     *
     * @var int
     */
    public $limit = 25;

    /**
     * If true, search engine should perform count even if that means extra lookup.
     *
     * @var bool
     */
    public $performCount = true;
}

class_alias(URLQuery::class, 'eZ\Publish\API\Repository\Values\URL\URLQuery');
