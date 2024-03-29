<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\Id;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id\ParentContentType as ParentContentTypeMatcher;
use eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\BaseTest;

class ParentContentTypeTest extends BaseTest
{
    private const EXAMPLE_LOCATION_ID = 54;
    private const EXAMPLE_PARENT_LOCATION_ID = 2;

    /** @var \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id\ParentContentType */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new ParentContentTypeMatcher();
    }

    /**
     * Returns a Repository mock configured to return the appropriate ContentType object with given id.
     *
     * @param int $contentTypeId
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateRepositoryMockForContentTypeId($contentTypeId)
    {
        $parentContentInfo = $this->getContentInfoMock([
            'contentTypeId' => $contentTypeId,
            'mainLocationId' => self::EXAMPLE_LOCATION_ID,
        ]);

        $parentLocation = $this->getLocationMock([
            'parentLocationId' => self::EXAMPLE_PARENT_LOCATION_ID,
        ]);
        $parentLocation->expects($this->once())
            ->method('getContentInfo')
            ->will(
                $this->returnValue($parentContentInfo)
            );

        $locationServiceMock = $this->createMock(LocationService::class);

        $locationServiceMock->expects($this->atLeastOnce())
            ->method('loadLocation')
            ->will(
                $this->returnValue($parentLocation)
            );
        // The following is used in the case of a match by contentInfo
        $locationServiceMock->expects($this->any())
            ->method('loadLocation')
            ->will(
                $this->returnValue($this->getLocationMock())
            );

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($locationServiceMock));
        $repository
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->will($this->returnValue($this->getPermissionResolverMock()));

        return $repository;
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id\ParentContentType::matchLocation
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     * @covers \eZ\Publish\Core\MVC\RepositoryAware::setRepository
     *
     * @param int|int[] $matchingConfig
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param bool $expectedResult
     */
    public function testMatchLocation($matchingConfig, Repository $repository, $expectedResult)
    {
        $this->matcher->setRepository($repository);
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame(
            $expectedResult,
            $this->matcher->matchLocation($this->getLocationMock(['parentLocationId' => self::EXAMPLE_LOCATION_ID]))
        );
    }

    public function matchLocationProvider()
    {
        return [
            [
                123,
                $this->generateRepositoryMockForContentTypeId(123),
                true,
            ],
            [
                123,
                $this->generateRepositoryMockForContentTypeId(456),
                false,
            ],
            [
                [123, 789],
                $this->generateRepositoryMockForContentTypeId(456),
                false,
            ],
            [
                [123, 789],
                $this->generateRepositoryMockForContentTypeId(789),
                true,
            ],
        ];
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id\ParentContentType::matchContentInfo
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     * @covers \eZ\Publish\Core\MVC\RepositoryAware::setRepository
     *
     * @param int|int[] $matchingConfig
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param bool $expectedResult
     */
    public function testMatchContentInfo($matchingConfig, Repository $repository, $expectedResult)
    {
        $this->matcher->setRepository($repository);
        $this->matcher->setMatchingConfig($matchingConfig);

        $this->assertSame(
            $expectedResult,
            $this->matcher->matchContentInfo($this->getContentInfoMock([
                'mainLocationId' => self::EXAMPLE_LOCATION_ID,
            ]))
        );
    }
}
