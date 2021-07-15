<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests\Iterator\BatchIteratorAdapter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentList;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use PHPUnit\Framework\TestCase;

final class ContentFilteringAdapterTest extends TestCase
{
    private const EXAMPLE_LANGUAGE_FILTER = ['eng-GB', 'pol-PL'];
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 25;

    public function testFetch(): void
    {
        $contentList = new ContentList(3, [
            $this->createMock(Content::class),
            $this->createMock(Content::class),
            $this->createMock(Content::class),
        ]);

        $originalFilter = new Filter();
        $originalFilter->withCriterion(new MatchAll());

        $expectedFilter = new Filter();
        $expectedFilter->withCriterion(new MatchAll());
        $expectedFilter->sliceBy(self::EXAMPLE_LIMIT, self::EXAMPLE_OFFSET);

        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->expects($this->once())
            ->method('find')
            ->with($expectedFilter, self::EXAMPLE_LANGUAGE_FILTER)
            ->willReturn($contentList);

        $adapter = new ContentFilteringAdapter($contentService, $originalFilter, self::EXAMPLE_LANGUAGE_FILTER);

        $this->assertSame(
            $contentList->getIterator(),
            $adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );

        // Input $filter reminds untouched
        $this->assertEquals(0, $originalFilter->getOffset());
        $this->assertEquals(0, $originalFilter->getLimit());
    }
}
