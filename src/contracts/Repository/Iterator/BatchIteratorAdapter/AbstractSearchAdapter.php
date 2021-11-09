<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Iterator;

abstract class AbstractSearchAdapter implements BatchIteratorAdapter
{
    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    protected $searchService;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query */
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

class_alias(AbstractSearchAdapter::class, 'eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter\AbstractSearchAdapter');
