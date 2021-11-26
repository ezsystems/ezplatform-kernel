<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias as UrlAliasMatcher;

class UrlAliasTest extends BaseTest
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new UrlAliasMatcher();
    }

    /**
     * @dataProvider setMatchingConfigProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias::setMatchingConfig
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     *
     * @param string $matchingConfig
     * @param string[] $expectedValues
     */
    public function testSetMatchingConfig($matchingConfig, $expectedValues)
    {
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame(
            $this->matcher->getValues(),
            $expectedValues
        );
    }

    public function setMatchingConfigProvider()
    {
        return [
            ['/foo/bar/', ['foo/bar']],
            ['/foo/bar/', ['foo/bar']],
            ['/foo/bar', ['foo/bar']],
            [['/foo/bar/', 'baz/biz/'], ['foo/bar', 'baz/biz']],
            [['foo/bar', 'baz/biz'], ['foo/bar', 'baz/biz']],
        ];
    }

    /**
     * Returns a Repository mock configured to return the appropriate Section object with given section identifier.
     *
     * @param string $path
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateRepositoryMockForUrlAlias($path)
    {
        // First an url alias that will never match, then the right url alias.
        // This ensures to test even if the location has several url aliases.
        $urlAliasList = [
            $this->createMock(URLAlias::class),
            $this
                ->getMockBuilder(URLAlias::class)
                ->setConstructorArgs([['path' => $path]])
                ->getMockForAbstractClass(),
        ];

        $urlAliasServiceMock = $this->createMock(URLAliasService::class);
        $urlAliasServiceMock->expects($this->at(0))
            ->method('listLocationAliases')
            ->with(
                $this->isInstanceOf(Location::class),
                true
            )
            ->will($this->returnValue([]));
        $urlAliasServiceMock->expects($this->at(1))
            ->method('listLocationAliases')
            ->with(
                $this->isInstanceOf(Location::class),
                false
            )
            ->will($this->returnValue($urlAliasList));

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('getURLAliasService')
            ->will($this->returnValue($urlAliasServiceMock));

        return $repository;
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias::matchLocation
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias::setMatchingConfig
     * @covers \Ibexa\Core\MVC\RepositoryAware::setRepository
     *
     * @param string|string[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param bool $expectedResult
     */
    public function testMatchLocation($matchingConfig, Repository $repository, $expectedResult)
    {
        $this->matcher->setRepository($repository);
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame(
            $expectedResult,
            $this->matcher->matchLocation($this->getLocationMock())
        );
    }

    public function matchLocationProvider()
    {
        return [
            [
                'foo/url',
                $this->generateRepositoryMockForUrlAlias('/foo/url'),
                true,
            ],
            [
                '/foo/url',
                $this->generateRepositoryMockForUrlAlias('/foo/url'),
                true,
            ],
            [
                'foo/url',
                $this->generateRepositoryMockForUrlAlias('/bar/url'),
                false,
            ],
            [
                ['foo/url', 'baz'],
                $this->generateRepositoryMockForUrlAlias('/bar/url'),
                false,
            ],
            [
                ['foo/url   ', 'baz   '],
                $this->generateRepositoryMockForUrlAlias('/baz'),
                true,
            ],
        ];
    }

    /**
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias::matchContentInfo
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias::setMatchingConfig
     */
    public function testMatchContentInfo()
    {
        $this->expectException(\RuntimeException::class);

        $this->matcher->setMatchingConfig('foo/bar');
        $this->matcher->matchContentInfo($this->getContentInfoMock());
    }
}

class_alias(UrlAliasTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\UrlAliasTest');
