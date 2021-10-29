<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

final class FixedSearchResultHitAdapter implements SearchResultAdapter
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult */
    private $searchResult;

    public function __construct(SearchResult $searchResult)
    {
        $this->searchResult = $searchResult;
    }

    public function getNbResults(): int
    {
        return $this->searchResult->totalCount ?? -1;
    }

    public function getSlice($offset, $length)
    {
        return $this->searchResult->searchHits;
    }

    public function getAggregations(): AggregationResultCollection
    {
        return $this->searchResult->aggregations;
    }

    public function getTime(): ?float
    {
        return $this->searchResult->time;
    }

    public function getTimedOut(): ?bool
    {
        return $this->searchResult->timedOut;
    }

    public function getMaxScore(): ?float
    {
        return $this->searchResult->maxScore;
    }
}

class_alias(FixedSearchResultHitAdapter::class, 'eZ\Publish\Core\Pagination\Pagerfanta\FixedSearchResultHitAdapter');
