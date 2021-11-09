<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

final class ContentSearchAdapter extends AbstractSearchAdapter
{
    protected function executeSearch(Query $query): SearchResult
    {
        return $this->searchService->findContent(
            $query,
            $this->languageFilter,
            $this->filterOnUserPermissions
        );
    }
}

class_alias(ContentSearchAdapter::class, 'eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter\ContentSearchAdapter');
