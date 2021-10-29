<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Contract for {@see \Ibexa\Contracts\Core\Repository\SearchService} based adapters.
 */
interface SearchResultAdapter extends AdapterInterface
{
    /**
     * Get results of aggregations associated with search query.
     *
     * Generates addition query if called before AdapterInterface::getSlice or AdapterInterface::getNbResults.
     */
    public function getAggregations(): AggregationResultCollection;

    /**
     * Get the duration of the search processing for current results slice (in s).
     *
     * Returns null if called before AdapterInterface::getSlice.
     */
    public function getTime(): ?float;

    /**
     * Indicates if the search has timed out for current results slice.
     *
     * Returns null if called before AdapterInterface::getSlice.
     */
    public function getTimedOut(): ?bool;

    /**
     * Return the maximum score or `null` if query wasn't executed.
     *
     * Returns null if called before AdapterInterface::getSlice.
     */
    public function getMaxScore(): ?float;
}

class_alias(SearchResultAdapter::class, 'eZ\Publish\Core\Pagination\Pagerfanta\SearchResultAdapter');
