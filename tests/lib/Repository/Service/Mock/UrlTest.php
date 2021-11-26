<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Service\Mock;

use DateTime;
use Ibexa\Contracts\Core\Persistence\URL\URL as SpiUrl;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query as ContentQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult as ContentSearchResults;
use Ibexa\Contracts\Core\Repository\Values\URL\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Repository\URLService;
use Ibexa\Tests\Core\Repository\Service\Mock\Base as BaseServiceMockTest;

class UrlTest extends BaseServiceMockTest
{
    private const URL_ID = 12;
    private const URL_EZ_NO = 'http://ez.no';
    private const URL_EZ_COM = 'http://ez.com';

    /** @var \Ibexa\Contracts\Core\Repository\URLService|\PHPUnit\Framework\MockObject\MockObject */
    private $urlHandler;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->urlHandler = $this->getPersistenceMockHandler('URL\\Handler');
        $this->permissionResolver = $this->getPermissionResolverMock();
    }

    public function testFindUrlsUnauthorized()
    {
        $this->configureUrlViewPermissionForHasAccess(false);

        $this->expectException(UnauthorizedException::class);
        $this->createUrlService()->findUrls(new URLQuery());
    }

    public function testFindUrlsNonNumericOffset()
    {
        $this->expectException(InvalidArgumentValue::class);

        $query = new URLQuery();
        $query->offset = 'foo';

        $this->createUrlService()->findUrls($query);
    }

    public function testFindUrlsNonNumericLimit()
    {
        $this->expectException(InvalidArgumentValue::class);

        $query = new URLQuery();
        $query->limit = 'foo';

        $this->createUrlService()->findUrls($query);
    }

    public function testFindUrls()
    {
        $url = $this->getApiUrl();

        $this->configureUrlViewPermissionForHasAccess(true);

        $query = new URLQuery();

        $results = [
            'count' => 1,
            'items' => [
                new SpiUrl(),
            ],
        ];

        $expected = new SearchResult([
            'totalCount' => 1,
            'items' => [$url],
        ]);

        $this->urlHandler
            ->expects($this->once())
            ->method('find')
            ->with($query)
            ->willReturn($results);

        $this->assertEquals($expected, $this->createUrlService()->findUrls($query));
    }

    public function testUpdateUrlUnauthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $url = $this->getApiUrl();

        $this->configureUrlUpdatePermission($url, false);

        $this->createUrlService()->updateUrl($url, new URLUpdateStruct());
    }

    public function testUpdateUrlNonUnique()
    {
        $this->expectException(InvalidArgumentException::class);

        $url = $this->getApiUrl(self::URL_ID, self::URL_EZ_NO);

        $this->configureUrlUpdatePermission($url, true);

        $struct = new URLUpdateStruct([
            'url' => self::URL_EZ_COM,
        ]);

        $urlService = $this->createUrlService(['isUnique']);
        $urlService
            ->expects($this->once())
            ->method('isUnique')
            ->with($url->id, $struct->url)
            ->willReturn(false);

        $urlService->updateUrl($url, $struct);
    }

    public function testUpdateUrl()
    {
        $apiUrl = $this->getApiUrl(self::URL_ID, self::URL_EZ_NO);
        $apiStruct = new URLUpdateStruct([
            'url' => self::URL_EZ_COM,
            'isValid' => false,
            'lastChecked' => new DateTime(),
        ]);

        $this->configurePermissions([
            ['url', 'update', $apiUrl, []],
            ['url', 'view', $apiUrl, []],
            ['url', 'view', new URL(['id' => self::URL_ID, 'url' => self::URL_EZ_COM, 'isValid' => true]), []],
        ]);

        $urlService = $this->createUrlService(['isUnique']);
        $urlService
            ->expects($this->once())
            ->method('isUnique')
            ->with($apiUrl->id, $apiStruct->url)
            ->willReturn(true);

        $this->urlHandler
            ->expects($this->once())
            ->method('updateUrl')
            ->willReturnCallback(function ($id, $struct) use ($apiUrl, $apiStruct) {
                $this->assertEquals($apiUrl->id, $id);

                $this->assertEquals($apiStruct->url, $struct->url);
                $this->assertEquals(0, $struct->lastChecked);
                $this->assertTrue($struct->isValid);
            });

        $this->urlHandler
            ->method('loadById')
            ->with($apiUrl->id)
            ->willReturnOnConsecutiveCalls(
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiUrl->url,
                    'isValid' => $apiUrl->isValid,
                    'lastChecked' => $apiUrl->lastChecked,
                ]),
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiStruct->url,
                    'isValid' => true,
                    'lastChecked' => 0,
                ])
            );

        $this->assertEquals(new URL([
            'id' => $apiUrl->id,
            'url' => $apiStruct->url,
            'isValid' => true,
            'lastChecked' => null,
        ]), $urlService->updateUrl($apiUrl, $apiStruct));
    }

    public function testUpdateUrlStatus()
    {
        $apiUrl = $this->getApiUrl(self::URL_ID, self::URL_EZ_NO);
        $apiStruct = new URLUpdateStruct([
            'isValid' => true,
            'lastChecked' => new DateTime('@' . time()),
        ]);

        $urlAfterUpdate = new URL([
            'id' => self::URL_ID,
            'url' => self::URL_EZ_NO,
            'isValid' => true,
            'lastChecked' => new DateTime('@' . time()),
        ]);

        $this->configurePermissions([
            ['url', 'update', $apiUrl, []],
            ['url', 'view', $apiUrl, []],
            ['url', 'view', $urlAfterUpdate, []],
        ]);

        $urlService = $this->createUrlService(['isUnique']);

        $this->urlHandler
            ->expects($this->once())
            ->method('updateUrl')
            ->willReturnCallback(function ($id, $struct) use ($apiUrl, $apiStruct) {
                $this->assertEquals($apiUrl->id, $id);

                $this->assertEquals($apiUrl->url, $struct->url);
                $this->assertEquals($apiStruct->lastChecked->getTimestamp(), $struct->lastChecked);
                $this->assertTrue($apiStruct->isValid, $struct->isValid);
            });

        $this->urlHandler
            ->method('loadById')
            ->with($apiUrl->id)
            ->willReturnOnConsecutiveCalls(
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiUrl->url,
                    'isValid' => $apiUrl->isValid,
                    'lastChecked' => $apiUrl->lastChecked,
                ]),
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiUrl->url,
                    'isValid' => $apiStruct->isValid,
                    'lastChecked' => $apiStruct->lastChecked->getTimestamp(),
                ])
            );

        $this->assertEquals(new URL([
            'id' => $apiUrl->id,
            'url' => $apiUrl->url,
            'isValid' => $apiStruct->isValid,
            'lastChecked' => $apiStruct->lastChecked,
        ]), $urlService->updateUrl($apiUrl, $apiStruct));
    }

    public function testLoadByIdUnauthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $this->configureUrlViewPermission(
            new URL([
                'id' => self::URL_ID,
            ]),
            false
        );

        $this->urlHandler
            ->expects($this->once())
            ->method('loadById')
            ->with(self::URL_ID)
            ->willReturn(new SpiUrl([
                'id' => self::URL_ID,
            ]));

        $this->createUrlService()->loadById(self::URL_ID);
    }

    public function testLoadById()
    {
        $url = new URL([
            'id' => self::URL_ID,
        ]);

        $this->configureUrlViewPermission($url, true);

        $this->urlHandler
            ->expects($this->once())
            ->method('loadById')
            ->with(self::URL_ID)
            ->willReturn(new SpiUrl([
                'id' => self::URL_ID,
            ]));

        $this->assertEquals($url, $this->createUrlService()->loadById(self::URL_ID));
    }

    public function testLoadByUrlUnauthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $url = self::URL_EZ_NO;

        $this->configureUrlViewPermission(
            new URL([
                'id' => self::URL_ID,
            ]),
            false
        );

        $this->urlHandler
            ->expects($this->once())
            ->method('loadByUrl')
            ->with($url)
            ->willReturn(new SpiUrl([
                'id' => self::URL_ID,
            ]));

        $this->createUrlService()->loadByUrl(self::URL_EZ_NO);
    }

    public function testLoadByUrl()
    {
        $url = self::URL_EZ_NO;

        $apiUrl = new URL([
            'url' => $url,
        ]);

        $this->configureUrlViewPermission($apiUrl, true);

        $this->urlHandler
            ->expects($this->once())
            ->method('loadByUrl')
            ->with($url)
            ->willReturn(new SpiUrl([
                'url' => $url,
            ]));

        $this->assertEquals($apiUrl, $this->createUrlService()->loadByUrl($url));
    }

    /**
     * @dataProvider dateProviderForFindUsages
     */
    public function testFindUsages($offset, $limit, ContentQuery $expectedQuery, array $usages)
    {
        $url = $this->getApiUrl(self::URL_ID, self::URL_EZ_NO);

        if (!empty($usages)) {
            $searchService = $this->createMock(SearchService::class);
            $searchService
                ->expects($this->once())
                ->method('findContentInfo')
                ->willReturnCallback(function ($query) use ($expectedQuery, $usages) {
                    $this->assertEquals($expectedQuery, $query);

                    return new ContentSearchResults([
                        'searchHits' => array_map(static function ($id) {
                            return new SearchHit([
                                'valueObject' => new ContentInfo([
                                    'id' => $id,
                                ]),
                            ]);
                        }, $usages),
                        'totalCount' => count($usages),
                    ]);
                });

            $this->getRepositoryMock()
                ->expects($this->once())
                ->method('getSearchService')
                ->willReturn($searchService);
        }

        $this->urlHandler
            ->expects($this->once())
            ->method('findUsages')
            ->with($url->id)
            ->willReturn($usages);

        $usageSearchResult = $this->createUrlService()->findUsages($url, $offset, $limit);

        $this->assertInstanceOf(UsageSearchResult::class, $usageSearchResult);
        $this->assertEquals(count($usages), $usageSearchResult->totalCount);
        foreach ($usageSearchResult as $contentInfo) {
            $this->assertContains($contentInfo->id, $usages);
        }
    }

    public function dateProviderForFindUsages()
    {
        return [
            [
                10, -1, new ContentQuery([
                    'filter' => new ContentCriterion\MatchNone(),
                    'offset' => 10,
                ]), [],
            ],
            [
                10, -1, new ContentQuery([
                    'filter' => new ContentCriterion\LogicalAnd([
                        new ContentCriterion\ContentId([1, 2, 3]),
                        new ContentCriterion\Visibility(ContentCriterion\Visibility::VISIBLE),
                    ]),
                    'offset' => 10,
                ]), [1, 2, 3],
            ],
            [
                10, 10, new ContentQuery([
                    'filter' => new ContentCriterion\LogicalAnd([
                        new ContentCriterion\ContentId([1, 2, 3]),
                        new ContentCriterion\Visibility(ContentCriterion\Visibility::VISIBLE),
                    ]),
                    'offset' => 10,
                    'limit' => 10,
                ]), [1, 2, 3],
            ],
        ];
    }

    public function testCreateUpdateStruct()
    {
        $this->assertEquals(new URLUpdateStruct(), $this->createUrlService()->createUpdateStruct());
    }

    protected function configureUrlViewPermissionForHasAccess($hasAccess = false)
    {
        $this->permissionResolver
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->willReturn($hasAccess);
    }

    protected function configureUrlViewPermission($object, $hasAccess = false)
    {
        $this->permissionResolver
            ->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('url'),
                $this->equalTo('view'),
                $this->equalTo($object)
            )
            ->will($this->returnValue($hasAccess));
    }

    protected function configureUrlUpdatePermission($object, $hasAccess = false)
    {
        $this->permissionResolver
            ->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('url'),
                $this->equalTo('update'),
                $this->equalTo($object)
            )
            ->will($this->returnValue($hasAccess));
    }

    protected function configurePermissions(array $permissions)
    {
        $this->permissionResolver
            ->expects($this->exactly(count($permissions)))
            ->method('canUser')
            ->withConsecutive(...$permissions)
            ->willReturn(true);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\URLService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createUrlService(array $methods = null)
    {
        return $this
            ->getMockBuilder(URLService::class)
            ->setConstructorArgs([$this->getRepositoryMock(), $this->urlHandler, $this->permissionResolver])
            ->setMethods($methods)
            ->getMock();
    }

    private function getApiUrl($id = null, $url = null)
    {
        return new URL(['id' => $id, 'url' => $url]);
    }
}

class_alias(UrlTest::class, 'eZ\Publish\Core\Repository\Tests\Service\Mock\UrlTest');
