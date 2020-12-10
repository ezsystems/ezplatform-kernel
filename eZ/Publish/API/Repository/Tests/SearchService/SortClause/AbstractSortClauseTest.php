<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests\SearchService\SortClause;

use eZ\Publish\API\Repository\Tests\BaseTest;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

abstract class AbstractSortClauseTest extends BaseTest
{
    protected function assertSearchResultOrderByRemoteId(
        array $expectedOrderedIds,
        SearchResult $actualSearchResults
    ): void {
        self::assertEquals(
            count($expectedOrderedIds),
            $actualSearchResults->totalCount
        );

        $actualIds = array_map(
            static function (SearchHit $searchHit): string {
                return $searchHit->valueObject->remoteId;
            },
            $actualSearchResults->searchHits
        );

        self::assertEquals($expectedOrderedIds, $actualIds);
    }
}
