<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\URLWildcardService;

use eZ\Publish\API\Repository\Tests\BaseTest;
use eZ\Publish\API\Repository\Values\URL\Query\Criterion as CriterionURL;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\URLWildcardQuery;

/**
 * Test case criterion for URLWildcard.
 *
 * @covers \eZ\Publish\API\Repository\URLWildcardService
 * @group url-wildcard
 */
class CriterionTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $repository = $this->getRepository();
        $urlWildcardService = $repository->getURLWildcardService();

        foreach ($this->getUrlWildcard() as $urlWildcard) {
            $urlWildcardService->create($urlWildcard['sourceUrl'], $urlWildcard['destinationUrl'], $urlWildcard['forward']);
        }
    }

    protected function findUrlWildcards(
        URLWildcardQuery $query,
        ?int $expectedTotalCount
    ): SearchResult {
        $repository = $this->getRepository();
        $searchResult = $repository->getURLWildcardService()->findUrlWildcards($query);

        $this->assertInstanceOf(SearchResult::class, $searchResult);
        $this->assertSame($expectedTotalCount, $searchResult->totalCount);
        $this->assertCount($expectedTotalCount, $searchResult->items);

        return $searchResult;
    }

    private function getUrlWildcard(bool $isAbsolute = false): array
    {
        $slash = $isAbsolute ? '/' : '';

        return [
            [
                'sourceUrl' => $slash . 'test',
                'destinationUrl' => $slash . 'content-test',
                'forward' => true,
            ],
            [
                'sourceUrl' => $slash . 'test test',
                'destinationUrl' => $slash . 'content test',
                'forward' => true,
            ],
            [
                'sourceUrl' => $slash . 'ibexa-dxp',
                'destinationUrl' => $slash . 'ibexa-1-2-3',
                'forward' => true,
            ],
            [
                'sourceUrl' => $slash . 'nice-url-seo',
                'destinationUrl' => $slash . '1/2/3/4',
                'forward' => false,
            ],
            [
                'sourceUrl' => $slash . 'no-forward test url',
                'destinationUrl' => $slash . 'no/forward test url',
                'forward' => false,
            ],
            [
                'sourceUrl' => $slash . 'Twitter',
                'destinationUrl' => $slash . 'a/b/c',
                'forward' => false,
            ],
            [
                'sourceUrl' => $slash . 'facebook',
                'destinationUrl' => $slash . '2/3/facebook',
                'forward' => true,
            ],
        ];
    }

    public function testMatchAll(): void
    {
        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchAll();

        $expectedWildcardUrls = $this->getUrlWildcard(true);
        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));

        foreach ($searchResult->items as $item) {
            $wildcard = [
                'sourceUrl' => $item->sourceUrl,
                'destinationUrl' => $item->destinationUrl,
                'forward' => $item->forward,
            ];

            $this->assertContains($wildcard, $expectedWildcardUrls);
        }
    }

    public function testMatchNone(): void
    {
        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchNone();

        $this->findUrlWildcards($query, 0);
    }

    public function testSourceUrl(): void
    {
        $expectedWildcardUrls = [
            '/test',
            '/test test',
            '/no-forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\SourceUrl('test');

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    public function testSourceUrlWithSpace(): void
    {
        $expectedWildcardUrls = [
            '/test test',
            '/no-forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\SourceUrl(' test');

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    public function testDestinationUrl(): void
    {
        $expectedWildcardUrls = [
            '/content-test',
            '/content test',
            '/no/forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\DestinationUrl('test');

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls, false);
    }

    public function testDestinationUrlWithSpace(): void
    {
        $expectedWildcardUrls = [
            '/content test',
            '/no/forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\DestinationUrl(' test');

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls, false);
    }

    public function testTypeForward(): void
    {
        $expectedWildcardUrls = [
            '/test',
            '/test test',
            '/ibexa-dxp',
            '/facebook',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\Type(true);

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    public function testTypeNoForward(): void
    {
        $expectedWildcardUrls = [
            '/nice-url-seo',
            '/no-forward test url',
            '/Twitter',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\Type(false);

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    public function testInvalidLimitThrowsInvalidArgumentException()
    {
        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchAll();
        $query->limit = 'invalid!';

        $repository = $this->getRepository();
        $urlWildcardService = $repository->getURLWildcardService();

        $this->expectException(InvalidArgumentValue::class);
        $urlWildcardService->findUrlWildcards($query);
    }

    public function testInvalidOffsetThrowsInvalidArgumentException()
    {
        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchAll();
        $query->offset = 'invalid!';

        $repository = $this->getRepository();
        $urlWildcardService = $repository->getURLWildcardService();

        $this->expectException(InvalidArgumentValue::class);
        $urlWildcardService->findUrlWildcards($query);
    }

    public function testSourceAndDestination(): void
    {
        $search = 'test';
        $expectedWildcardUrlsSource = [
            '/test',
            '/test test',
            '/no-forward test url',
        ];

        $expectedWildcardUrlsDestination = [
            '/content-test',
            '/content test',
            '/no/forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\SourceUrl($search),
            new Criterion\DestinationUrl($search),
        ]);

        $searchResult = $this->findUrlWildcards($query, count($expectedWildcardUrlsSource));

        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrlsSource);
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrlsDestination, false);
    }

    public function testLogicalInvalidCriterion(): void
    {
        $this->expectException(InvalidCriterionArgumentException::class);
        $this->expectExceptionMessage("You provided eZ\Publish\API\Repository\Values\URL\Query\Criterion\VisibleOnly at index '1', but only instances of 'Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion' are accepted");

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\SourceUrl('test'),
            new CriterionURL\VisibleOnly(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\URLWildcard[] $items
     * @param string[] $expectedWildcardUrls
     */
    protected function checkWildcardUrl(array $items, array $expectedWildcardUrls, bool $sourceUrl = true): void
    {
        foreach ($items as $item) {
            if ($sourceUrl) {
                $this->assertContains($item->sourceUrl, $expectedWildcardUrls);
            } else {
                $this->assertContains($item->destinationUrl, $expectedWildcardUrls);
            }
        }
    }
}
