<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;

use eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Iterator;

abstract class AbstractSearchAdapter implements BatchIteratorAdapter
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \eZ\Publish\API\Repository\Values\Content\Query */
    protected $query;

    /** @var string[] */
    protected $languageFilter;

    /** @var bool */
    protected $filterOnUserPermissions;

    public function __construct(
        SearchService $searchService,
        Query $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ) {
        $this->searchService = $searchService;
        $this->query = $query;
        $this->languageFilter = $languageFilter;
        $this->filterOnUserPermissions = $filterOnUserPermissions;
    }

    final public function fetch(int $offset, int $limit): Iterator
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $limit;

        return $this->executeSearch($query)->getIterator();
    }

    abstract protected function executeSearch(Query $query): SearchResult;
}
