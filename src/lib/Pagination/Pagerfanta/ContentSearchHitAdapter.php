<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

/**
 * Pagerfanta adapter for eZ Publish content search.
 * Will return results as SearchHit objects.
 */
class ContentSearchHitAdapter extends AbstractSearchResultAdapter
{
    protected function executeQuery(
        SearchService $searchService,
        Query $query,
        array $languageFilter
    ): SearchResult {
        return $searchService->findContent($query, $languageFilter);
    }
}

class_alias(ContentSearchHitAdapter::class, 'eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchHitAdapter');
