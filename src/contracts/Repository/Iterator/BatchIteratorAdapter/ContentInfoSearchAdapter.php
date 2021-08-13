<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

final class ContentInfoSearchAdapter extends AbstractSearchAdapter
{
    protected function executeSearch(Query $query): SearchResult
    {
        return $this->searchService->findContentInfo(
            $query,
            $this->languageFilter,
            $this->filterOnUserPermissions
        );
    }
}
