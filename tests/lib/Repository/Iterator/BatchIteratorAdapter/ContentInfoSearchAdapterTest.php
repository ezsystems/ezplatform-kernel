<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests\Iterator\BatchIteratorAdapter;

use eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter\AbstractSearchAdapter;
use eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter\ContentInfoSearchAdapter;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;

final class ContentInfoSearchAdapterTest extends AbstractSearchAdapterTest
{
    protected function createAdapterUnderTest(
        SearchService $searchService,
        Query $query,
        array $languageFilter,
        bool $filterOnPermissions
    ): AbstractSearchAdapter {
        return new ContentInfoSearchAdapter(
            $searchService,
            $query,
            self::EXAMPLE_LANGUAGE_FILTER,
            true
        );
    }

    protected function getExpectedFindMethod(): string
    {
        return 'findContentInfo';
    }
}
