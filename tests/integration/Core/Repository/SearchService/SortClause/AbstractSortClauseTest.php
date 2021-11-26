<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

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

class_alias(AbstractSortClauseTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\SortClause\AbstractSortClauseTest');
