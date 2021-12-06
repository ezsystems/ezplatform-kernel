<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use DateTime;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;

/**
 * Test case for operations in the UserService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\URLService
 * @group integration
 * @group url
 */
class URLServiceTest extends BaseURLServiceTest
{
    private const TOTAL_URLS_COUNT = 20;

    protected function setUp(): void
    {
        parent::setUp();

        $urls = [
            [
                'name' => 'Twitter',
                'url' => 'https://twitter.com/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Facebook',
                'url' => 'https://www.facebook.com/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Google',
                'url' => 'https://www.google.com/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Vimeo',
                'url' => 'https://vimeo.com/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Facebook Sharer',
                'url' => 'https://www.facebook.com/sharer.php',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Youtube',
                'url' => 'https://www.youtube.com/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Googel support',
                'url' => 'https://support.google.com/chrome/answer/95647?hl=es',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Instagram',
                'url' => 'https://instagram.com/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Discuz',
                'url' => 'https://www.discuz.net/forum.php',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Google calendar',
                'url' => 'https://calendar.google.com/calendar/render',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Wikipedia',
                'url' => 'https://www.wikipedia.org/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Google Analytics',
                'url' => 'https://www.google.com/analytics/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'nazwa.pl',
                'url' => 'https://www.nazwa.pl/',
                'published' => true,
                'sectionId' => 1,
            ],
            [
                'name' => 'Apache',
                'url' => 'https://www.apache.org/',
                'published' => true,
                'sectionId' => 2,
            ],
            [
                'name' => 'Nginx',
                'url' => 'https://www.nginx.com/',
                'published' => true,
                'sectionId' => 2,
            ],
            [
                'name' => 'Microsoft.com',
                'url' => 'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
                'published' => true,
                'sectionId' => 3,
            ],
            [
                'name' => 'Dropbox',
                'url' => 'https://www.dropbox.com/',
                'published' => false,
                'sectionId' => 3,
            ],
            [
                'name' => 'Google [DE]',
                'url' => 'https://www.google.de/',
                'published' => true,
                'sectionId' => 3,
            ],
        ];

        $repository = $this->getRepository();

        $parentLocationId = $this->generateId('location', 2);

        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();

        $contentType = $repository->getContentTypeService()->loadContentTypeByIdentifier('url');
        foreach ($urls as $data) {
            $struct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $struct->setField('name', $data['name']);
            $struct->setField('url', $data['url']);
            $struct->sectionId = $data['sectionId'];

            $location = $locationService->newLocationCreateStruct($parentLocationId);

            $draft = $contentService->createContent($struct, [$location]);
            if ($data['published']) {
                $contentService->publishVersion($draft->versionInfo);
            }
        }
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     */
    public function testFindUrls()
    {
        $expectedUrls = [
            'https://www.apache.org/',
            'https://calendar.google.com/calendar/render',
            'https://www.dropbox.com/',
            '/content/view/sitemap/2',
            'https://support.google.com/chrome/answer/95647?hl=es',
            'https://www.nazwa.pl/',
            'https://www.facebook.com/sharer.php',
            'https://www.wikipedia.org/',
            'https://www.google.de/',
            'https://www.google.com/',
            'https://www.nginx.com/',
            '/content/view/tagcloud/2',
            'https://www.youtube.com/',
            'https://vimeo.com/',
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://twitter.com/',
            'https://www.google.com/analytics/',
            'https://www.facebook.com/',
            'https://www.discuz.net/forum.php',
            'https://instagram.com/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     */
    public function testFindUrlsWithoutCounting()
    {
        $expectedUrls = [
            'https://www.apache.org/',
            'https://calendar.google.com/calendar/render',
            'https://www.dropbox.com/',
            '/content/view/sitemap/2',
            'https://support.google.com/chrome/answer/95647?hl=es',
            'https://www.nazwa.pl/',
            'https://www.facebook.com/sharer.php',
            'https://www.wikipedia.org/',
            'https://www.google.de/',
            'https://www.google.com/',
            'https://www.nginx.com/',
            '/content/view/tagcloud/2',
            'https://www.youtube.com/',
            'https://vimeo.com/',
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://twitter.com/',
            'https://www.google.com/analytics/',
            'https://www.facebook.com/',
            'https://www.discuz.net/forum.php',
            'https://instagram.com/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->performCount = false;

        $this->doTestFindUrls($query, $expectedUrls, null);
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingMatchNone()
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchNone();

        $this->doTestFindUrls($query, [], 0);
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingPatternCriterion()
    {
        $expectedUrls = [
            'https://www.google.de/',
            'https://www.google.com/',
            'https://support.google.com/chrome/answer/95647?hl=es',
            'https://calendar.google.com/calendar/render',
            'https://www.google.com/analytics/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\Pattern('google');

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingValidityCriterionValid()
    {
        $expectedUrls = [
            'https://www.google.com/',
            '/content/view/sitemap/2',
            'https://support.google.com/chrome/answer/95647?hl=es',
            'https://www.google.de/',
            'https://www.nginx.com/',
            'https://www.google.com/analytics/',
            'https://www.discuz.net/forum.php',
            'https://www.wikipedia.org/',
            'https://www.facebook.com/sharer.php',
            'https://twitter.com/',
            'https://www.nazwa.pl/',
            'https://instagram.com/',
            'https://www.apache.org/',
            'https://www.dropbox.com/',
            'https://www.facebook.com/',
            'https://www.youtube.com/',
            'https://calendar.google.com/calendar/render',
            'https://vimeo.com/',
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\Validity(true);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls
     * @depends testFindUrls
     */
    public function testFindUrlsUsingSectionIdCriterion(): void
    {
        $expectedUrls = [
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\SectionId([3]);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingSectionIdAndValidityCriterionValid(): void
    {
        $expectedUrls = [
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\SectionId([3]),
            new Criterion\Validity(true),
        ]);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls
     * @depends testFindUrls
     */
    public function testFindUrlsUsingSectionIdentifierCriterion(): void
    {
        $expectedUrls = [
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\SectionIdentifier(['media']);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingSectionIdentifierAndValidityCriterionValid(): void
    {
        $expectedUrls = [
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
            'https://www.apache.org/',
            'https://www.nginx.com/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\SectionIdentifier(['media', 'users']),
            new Criterion\Validity(true),
        ]);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingSectionIdentifierOrSectionIdCriterion(): void
    {
        $expectedUrls = [
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
            'https://www.apache.org/',
            'https://www.nginx.com/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\LogicalOr([
            new Criterion\SectionIdentifier(['media']),
            new Criterion\SectionId([2]),
        ]);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingValidityCriterionInvalid()
    {
        $expectedUrls = [
            '/content/view/tagcloud/2',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\Validity(false);

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsUsingVisibleOnlyCriterion()
    {
        $expectedUrls = [
            'https://vimeo.com/',
            'https://calendar.google.com/calendar/render',
            'https://www.facebook.com/',
            'https://www.google.com/',
            'https://www.google.com/analytics/',
            'https://www.facebook.com/sharer.php',
            'https://www.apache.org/',
            'https://www.nginx.com/',
            'https://www.wikipedia.org/',
            'https://www.youtube.com/',
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.google.de/',
            'https://instagram.com/',
            'https://www.nazwa.pl/',
            '/content/view/tagcloud/2',
            'https://www.discuz.net/forum.php',
            'https://support.google.com/chrome/answer/95647?hl=es',
            'https://twitter.com/',
            '/content/view/sitemap/2',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\VisibleOnly();

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls));
    }

    /**
     * @see https://jira.ez.no/browse/EZP-31059
     */
    public function testFindUrlsUsingVisibleOnlyCriterionReturnsUniqueItems(): void
    {
        $exampleUrl = 'https://ezplatform.com';

        $this->createContentWithLink('A', $exampleUrl);
        $this->createContentWithLink('B', $exampleUrl);

        $urlService = $this->getRepository()->getURLService();

        $query = new URLQuery();
        $query->filter = new Criterion\VisibleOnly();
        $query->limit = -1;

        $results = $urlService->findUrls($query);

        $this->assertSearchResultItemsAreUnique($results);
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     */
    public function testFindUrlsWithInvalidOffsetThrowsInvalidArgumentException()
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->offset = 'invalid!';

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $this->expectException(InvalidArgumentValue::class);
        $urlService->findUrls($query);
        /* END: Use Case */
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     */
    public function testFindUrlsWithInvalidLimitThrowsInvalidArgumentException()
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->limit = 'invalid!';

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $this->expectException(InvalidArgumentValue::class);
        $urlService->findUrls($query);
        /* END: Use Case */
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsWithOffset()
    {
        $expectedUrls = [
            'https://www.discuz.net/forum.php',
            'https://calendar.google.com/calendar/render',
            'https://www.wikipedia.org/',
            'https://www.google.com/analytics/',
            'https://www.nazwa.pl/',
            'https://www.apache.org/',
            'https://www.nginx.com/',
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->offset = 10;
        $query->sortClauses = [new SortClause\Id()];

        $this->doTestFindUrls($query, $expectedUrls, self::TOTAL_URLS_COUNT);
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsWithOffsetAndLimit()
    {
        $expectedUrls = [
            'https://www.discuz.net/forum.php',
            'https://calendar.google.com/calendar/render',
            'https://www.wikipedia.org/',
        ];

        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->offset = 10;
        $query->limit = 3;
        $query->sortClauses = [new SortClause\Id()];

        $this->doTestFindUrls($query, $expectedUrls, self::TOTAL_URLS_COUNT);
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     */
    public function testFindUrlsWithLimitZero()
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->limit = 0;

        $this->doTestFindUrls($query, [], self::TOTAL_URLS_COUNT);
    }

    /**
     * Test for URLService::findUrls() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::findUrls()
     * @depends testFindUrls
     * @dataProvider dataProviderForFindUrlsWithSorting
     */
    public function testFindUrlsWithSorting(SortClause $sortClause, array $expectedUrls)
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->sortClauses = [$sortClause];

        $this->doTestFindUrls($query, $expectedUrls, count($expectedUrls), false);
    }

    public function dataProviderForFindUrlsWithSorting()
    {
        $urlsSortedById = [
            '/content/view/sitemap/2',
            '/content/view/tagcloud/2',
            'https://twitter.com/',
            'https://www.facebook.com/',
            'https://www.google.com/',
            'https://vimeo.com/',
            'https://www.facebook.com/sharer.php',
            'https://www.youtube.com/',
            'https://support.google.com/chrome/answer/95647?hl=es',
            'https://instagram.com/',
            'https://www.discuz.net/forum.php',
            'https://calendar.google.com/calendar/render',
            'https://www.wikipedia.org/',
            'https://www.google.com/analytics/',
            'https://www.nazwa.pl/',
            'https://www.apache.org/',
            'https://www.nginx.com/',
            'https://windows.microsoft.com/en-US/internet-explorer/products/ie/home',
            'https://www.dropbox.com/',
            'https://www.google.de/',
        ];

        $urlsSortedByURL = $urlsSortedById;
        sort($urlsSortedByURL);

        return [
            [new SortClause\Id(SortClause::SORT_ASC), $urlsSortedById],
            [new SortClause\Id(SortClause::SORT_DESC), array_reverse($urlsSortedById)],
            [new SortClause\URL(SortClause::SORT_ASC), $urlsSortedByURL],
            [new SortClause\URL(SortClause::SORT_DESC), array_reverse($urlsSortedByURL)],
        ];
    }

    /**
     * Test for URLService::updateUrl() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::updateUrl()
     */
    public function testUpdateUrl()
    {
        $repository = $this->getRepository();

        $id = $this->generateId('url', 23);

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $urlBeforeUpdate = $urlService->loadById($id);
        $updateStruct = $urlService->createUpdateStruct();
        $updateStruct->url = 'https://someurl.com/';

        $urlAfterUpdate = $urlService->updateUrl($urlBeforeUpdate, $updateStruct);
        /* END: Use Case */

        $this->assertInstanceOf(URL::class, $urlAfterUpdate);
        $this->assertPropertiesCorrect([
            'id' => 23,
            'url' => 'https://someurl.com/',
            // (!) URL status should be reset to valid nad never checked
            'isValid' => true,
            'lastChecked' => null,
            'created' => new DateTime('@1343140541'),
        ], $urlAfterUpdate);
        $this->assertGreaterThanOrEqual($urlBeforeUpdate->modified, $urlAfterUpdate->modified);
    }

    /**
     * Test for URLService::updateUrl() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::updateUrl()
     */
    public function testUpdateUrlStatus()
    {
        $repository = $this->getRepository();

        $id = $this->generateId('url', 23);
        $checked = new DateTime('@' . time());

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $urlBeforeUpdate = $urlService->loadById($id);

        $updateStruct = $urlService->createUpdateStruct();
        $updateStruct->isValid = false;
        $updateStruct->lastChecked = $checked;

        $urlAfterUpdate = $urlService->updateUrl($urlBeforeUpdate, $updateStruct);
        /* END: Use Case */

        $this->assertInstanceOf(URL::class, $urlAfterUpdate);
        $this->assertPropertiesCorrect([
            'id' => $id,
            'url' => '/content/view/sitemap/2',
            // (!) URL status should be reset to valid nad never checked
            'isValid' => false,
            'lastChecked' => $checked,
            'created' => new DateTime('@1343140541'),
        ], $urlAfterUpdate);
        $this->assertGreaterThanOrEqual($urlBeforeUpdate->modified, $urlAfterUpdate->modified);
    }

    /**
     * Test for URLService::updateUrl() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::updateUrl()
     * @depends testUpdateUrl
     */
    public function testUpdateUrlWithNonUniqueUrl()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        $id = $this->generateId('url', 23);

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $urlBeforeUpdate = $urlService->loadById($id);
        $updateStruct = $urlService->createUpdateStruct();
        $updateStruct->url = 'https://www.youtube.com/';

        // This call will fail with a InvalidArgumentException
        $urlService->updateUrl($urlBeforeUpdate, $updateStruct);
        /* END: Use Case */
    }

    /**
     * Test for URLService::loadById() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::loadById
     */
    public function testLoadById()
    {
        $repository = $this->getRepository();

        $id = $this->generateId('url', 23);

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $url = $urlService->loadById($id);
        /* END: Use Case */

        $this->assertInstanceOf(URL::class, $url);
        $this->assertPropertiesCorrect([
            'id' => 23,
            'url' => '/content/view/sitemap/2',
            'isValid' => true,
            'lastChecked' => null,
            'created' => new DateTime('@1343140541'),
            'modified' => new DateTime('@1343140541'),
        ], $url);
    }

    /**
     * Test for URLService::loadById() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::loadById
     * @depends testLoadById
     */
    public function testLoadByIdThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        $nonExistentUrlId = $this->generateId('url', self::DB_INT_MAX);
        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $this->expectException(NotFoundException::class);
        $urlService->loadById($nonExistentUrlId);
        /* END: Use Case */
    }

    /**
     * Test for URLService::loadByUrl() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::loadByUrl
     */
    public function testLoadByUrl()
    {
        $repository = $this->getRepository();

        $urlAddr = '/content/view/sitemap/2';
        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $url = $urlService->loadByUrl($urlAddr);

        /* END: Use Case */

        $this->assertInstanceOf(URL::class, $url);
        $this->assertPropertiesCorrect([
            'id' => 23,
            'url' => '/content/view/sitemap/2',
            'isValid' => true,
            'lastChecked' => null,
            'created' => new DateTime('@1343140541'),
            'modified' => new DateTime('@1343140541'),
        ], $url);
    }

    /**
     * Test for URLService::loadByUrl() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::loadByUrl
     * @depends testLoadByUrl
     */
    public function testLoadByUrlThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        $nonExistentUrl = 'https://laravel.com/';
        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $this->expectException(NotFoundException::class);
        $urlService->loadByUrl($nonExistentUrl);
        /* END: Use Case */
    }

    /**
     * Test for URLService::createUpdateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLService::createUpdateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct
     */
    public function testCreateUpdateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();
        $updateStruct = $urlService->createUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(URLUpdateStruct::class, $updateStruct);

        return $updateStruct;
    }

    /**
     * Test for URLService::createUpdateStruct() method.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct $updateStruct
     * @depends testCreateUpdateStruct
     */
    public function testCreateUpdateStructValues(URLUpdateStruct $updateStruct)
    {
        $this->assertPropertiesCorrect([
            'url' => null,
            'isValid' => null,
            'lastChecked' => null,
        ], $updateStruct);
    }

    /**
     * Test for URLService::testFindUsages() method.
     *
     * @depends testLoadById
     * @dataProvider dataProviderForFindUsages
     */
    public function testFindUsages($urlId, $offset, $limit, array $expectedContentInfos, $expectedTotalCount = null)
    {
        $repository = $this->getRepository();

        $id = $this->generateId('url', $urlId);
        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $loadedUrl = $urlService->loadById($id);

        $usagesSearchResults = $urlService->findUsages($loadedUrl, $offset, $limit);
        /* END: Use Case */

        $this->assertInstanceOf(UsageSearchResult::class, $usagesSearchResults);
        $this->assertEquals($expectedTotalCount, $usagesSearchResults->totalCount);
        $this->assertUsagesSearchResultItems($usagesSearchResults, $expectedContentInfos);
    }

    public function dataProviderForFindUsages()
    {
        return [
            // findUsages($url, 0, -1)
            [23, 0, -1, [54], 1],
            // findUsages($url, 0, $limit)
            [23, 0, 1, [54], 1],
        ];
    }

    /**
     * Test for URLService::testFindUsages() method.
     *
     * @depends testFindUsages
     */
    public function testFindUsagesReturnsEmptySearchResults()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $urlService = $repository->getURLService();

        $loadedUrl = $urlService->loadByUrl('https://www.dropbox.com/');

        $usagesSearchResults = $urlService->findUsages($loadedUrl);
        /* END: Use Case */

        $this->assertInstanceOf(UsageSearchResult::class, $usagesSearchResults);
        $this->assertPropertiesCorrect([
            'totalCount' => 0,
            'items' => [],
        ], $usagesSearchResults);
    }
}

class_alias(URLServiceTest::class, 'eZ\Publish\API\Repository\Tests\URLServiceTest');
