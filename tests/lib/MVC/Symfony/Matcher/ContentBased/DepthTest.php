<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Depth as DepthMatcher;

class DepthTest extends BaseTest
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Depth */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new DepthMatcher();
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Depth::matchLocation
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     *
     * @param int|int[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param bool $expectedResult
     */
    public function testMatchLocation($matchingConfig, Location $location, $expectedResult)
    {
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame($expectedResult, $this->matcher->matchLocation($location));
    }

    public function matchLocationProvider()
    {
        return [
            [
                1,
                $this->getLocationMock(['depth' => 1]),
                true,
            ],
            [
                1,
                $this->getLocationMock(['depth' => 2]),
                false,
            ],
            [
                [1, 3],
                $this->getLocationMock(['depth' => 2]),
                false,
            ],
            [
                [1, 3],
                $this->getLocationMock(['depth' => 3]),
                true,
            ],
            [
                [1, 3],
                $this->getLocationMock(['depth' => 0]),
                false,
            ],
            [
                [0, 1],
                $this->getLocationMock(['depth' => 0]),
                true,
            ],
        ];
    }

    /**
     * @dataProvider matchContentInfoProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Depth::matchContentInfo
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     * @covers \Ibexa\Core\MVC\RepositoryAware::setRepository
     *
     * @param int|int[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param bool $expectedResult
     */
    public function testMatchContentInfo($matchingConfig, Repository $repository, $expectedResult)
    {
        $this->matcher->setRepository($repository);
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame(
            $expectedResult,
            $this->matcher->matchContentInfo($this->getContentInfoMock(['mainLocationId' => 42]))
        );
    }

    public function matchContentInfoProvider()
    {
        return [
            [
                1,
                $this->generateRepositoryMockForDepth(1),
                true,
            ],
            [
                1,
                $this->generateRepositoryMockForDepth(2),
                false,
            ],
            [
                [1, 3],
                $this->generateRepositoryMockForDepth(2),
                false,
            ],
            [
                [1, 3],
                $this->generateRepositoryMockForDepth(3),
                true,
            ],
        ];
    }

    /**
     * Returns a Repository mock configured to return the appropriate Location object with given parent location Id.
     *
     * @param int $depth
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateRepositoryMockForDepth($depth)
    {
        $locationServiceMock = $this->createMock(LocationService::class);
        $locationServiceMock->expects($this->once())
            ->method('loadLocation')
            ->with(42)
            ->will(
                $this->returnValue(
                    $this->getLocationMock(['depth' => $depth])
                )
            );

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('getLocationService')
            ->will($this->returnValue($locationServiceMock));
        $repository
            ->expects($this->once())
            ->method('getPermissionResolver')
            ->will($this->returnValue($this->getPermissionResolverMock()));

        return $repository;
    }
}

class_alias(DepthTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\DepthTest');
