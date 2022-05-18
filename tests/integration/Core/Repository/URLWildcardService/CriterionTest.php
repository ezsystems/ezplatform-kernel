<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\URLWildcardService;

use eZ\Publish\API\Repository\Tests\BaseTest;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\URLWildcardQuery;

/**
 * Test case criterion for URLWildcard.
 *
 * @see \eZ\Publish\API\Repository\URLWildcardService
 * @group url-wildcard
 */
class CriterionTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $urlWildcards = [
            [
                'source' => 'test',
                'destination' => 'contentTest',
                'forward' => true,
            ],
            [
                'source' => 'test test',
                'destination' => 'content test',
                'forward' => true,
            ],
            [
                'source' => 'ibexa-dxp',
                'destination' => 'ibexa-1-2-3',
                'forward' => true,
            ],
            [
                'source' => 'nice-url-seo',
                'destination' => '1/2/3/4',
                'forward' => false,
            ],
            [
                'source' => 'no-forward test url',
                'destination' => 'no/forward test url',
                'forward' => false,
            ],
            [
                'source' => 'Twitter',
                'destination' => 'a/b/c',
                'forward' => false,
            ],
            [
                'source' => 'facebook',
                'destination' => '2/3/facebook',
                'forward' => true,
            ],
        ];

        $repository = $this->getRepository();
        $urlWildcardService = $repository->getURLWildcardService();

        foreach ($urlWildcards as $urlWildcard) {
            $urlWildcardService->create($urlWildcard['source'], $urlWildcard['destination'], $urlWildcard['forward']);
        }
    }

    protected function doTestFindUrlWildcards(
        URLWildcardQuery $query,
        array $expectedWildcards,
        ?int $expectedTotalCount
    ): SearchResult {
        $repository = $this->getRepository();
        $searchResult = $repository->getURLWildcardService()->findUrlWildcards($query);

        $this->assertInstanceOf(SearchResult::class, $searchResult);
        $this->assertSame($expectedTotalCount, $searchResult->totalCount);
        $this->assertCount(count($expectedWildcards), $searchResult->items);

        return $searchResult;
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testMatchAll(): void
    {
        $expectedWildcardUrls = [
            [
                'sourceUrl' => '/test',
                'destinationUrl' => '/contentTest',
                'forward' => true,
            ],
            [
                'sourceUrl' => '/test test',
                'destinationUrl' => '/content test',
                'forward' => true,
            ],
            [
                'sourceUrl' => '/ibexa-dxp',
                'destinationUrl' => '/ibexa-1-2-3',
                'forward' => true,
            ],
            [
                'sourceUrl' => '/nice-url-seo',
                'destinationUrl' => '/1/2/3/4',
                'forward' => false,
            ],
            [
                'sourceUrl' => '/no-forward test url',
                'destinationUrl' => '/no/forward test url',
                'forward' => false,
            ],
            [
                'sourceUrl' => '/Twitter',
                'destinationUrl' => '/a/b/c',
                'forward' => false,
            ],
            [
                'sourceUrl' => '/facebook',
                'destinationUrl' => '/2/3/facebook',
                'forward' => true,
            ],
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchAll();

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));

        foreach ($searchResult->items as $item) {
            $wildcard = [
                'sourceUrl' => $item->sourceUrl,
                'destinationUrl' => $item->destinationUrl,
                'forward' => $item->forward,
            ];

            $this->assertContains($wildcard, $expectedWildcardUrls);
        }
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testMatchNone(): void
    {
        $query = new URLWildcardQuery();
        $query->filter = new Criterion\MatchNone();

        $this->doTestFindUrlWildcards($query, [], 0);
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testSourceUrl(): void
    {
        $expectedWildcardUrls = [
            '/test',
            '/test test',
            '/no-forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\SourceUrl('test');

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testSourceUrlWithSpace(): void
    {
        $expectedWildcardUrls = [
            '/test test',
            '/no-forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\SourceUrl(' test');

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testDestinationUrl(): void
    {
        $expectedWildcardUrls = [
            '/contentTest',
            '/content test',
            '/no/forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\DestinationUrl('test');

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls, false);
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testDestinationUrlWithSpace(): void
    {
        $expectedWildcardUrls = [
            '/content test',
            '/no/forward test url',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\DestinationUrl(' test');

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls, false);
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
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

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
    }

    /**
     * @see \eZ\Publish\Core\Repository\URLWildcardService::findUrlWildcards()
     */
    public function testTypeNoForward(): void
    {
        $expectedWildcardUrls = [
            '/nice-url-seo',
            '/no-forward test url',
            '/Twitter',
        ];

        $query = new URLWildcardQuery();
        $query->filter = new Criterion\Type(false);

        $searchResult = $this->doTestFindUrlWildcards($query, $expectedWildcardUrls, count($expectedWildcardUrls));
        $this->checkWildcardUrl($searchResult->items, $expectedWildcardUrls);
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
