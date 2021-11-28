<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\Id;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\ContentTypeGroup as ContentTypeGroupIdMatcher;
use Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\BaseTest;

class ContentTypeGroupTest extends BaseTest
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\ContentTypeGroup */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new ContentTypeGroupIdMatcher();
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\ContentTypeGroup::matchLocation
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     *
     * @param int|int[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param bool $expectedResult
     */
    public function testMatchLocation($matchingConfig, Repository $repository, $expectedResult)
    {
        $this->matcher->setRepository($repository);
        $this->matcher->setMatchingConfig($matchingConfig);

        $this->assertSame(
            $expectedResult,
            $this->matcher->matchLocation($this->generateLocationMock())
        );
    }

    public function matchLocationProvider()
    {
        $data = [];

        $data[] = [
            123,
            $this->generateRepositoryMockForContentTypeGroupId(123),
            true,
        ];

        $data[] = [
            123,
            $this->generateRepositoryMockForContentTypeGroupId(456),
            false,
        ];

        $data[] = [
            [123, 789],
            $this->generateRepositoryMockForContentTypeGroupId(456),
            false,
        ];

        $data[] = [
            [123, 789],
            $this->generateRepositoryMockForContentTypeGroupId(789),
            true,
        ];

        return $data;
    }

    /**
     * Generates a Location mock.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateLocationMock()
    {
        $location = $this->getLocationMock();
        $location
            ->expects($this->any())
            ->method('getContentInfo')
            ->will(
                $this->returnValue(
                    $this->getContentInfoMock(['contentTypeId' => 42])
                )
            );

        return $location;
    }

    /**
     * @dataProvider matchContentInfoProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\ContentTypeGroup::matchContentInfo
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
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
            $this->matcher->matchContentInfo($this->getContentInfoMock(['contentTypeId' => 42]))
        );
    }

    public function matchContentInfoProvider()
    {
        $data = [];

        $data[] = [
            123,
            $this->generateRepositoryMockForContentTypeGroupId(123),
            true,
        ];

        $data[] = [
            123,
            $this->generateRepositoryMockForContentTypeGroupId(456),
            false,
        ];

        $data[] = [
            [123, 789],
            $this->generateRepositoryMockForContentTypeGroupId(456),
            false,
        ];

        $data[] = [
            [123, 789],
            $this->generateRepositoryMockForContentTypeGroupId(789),
            true,
        ];

        return $data;
    }

    /**
     * Returns a Repository mock configured to return the appropriate Location object with given parent location Id.
     *
     * @param int $contentTypeGroupId
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateRepositoryMockForContentTypeGroupId($contentTypeGroupId)
    {
        $contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $contentTypeMock = $this->getMockForAbstractClass(ContentType::class);
        $contentTypeServiceMock->expects($this->once())
            ->method('loadContentType')
            ->with(42)
            ->will($this->returnValue($contentTypeMock));
        $contentTypeMock->expects($this->once())
            ->method('getContentTypeGroups')
            ->will(
                $this->returnValue(
                    [
                        // First a group that will never match, then the right group.
                        // This ensures to test even if the content type belongs to several groups at once.
                        $this->getMockForAbstractClass(ContentTypeGroup::class),
                        $this
                            ->getMockBuilder(ContentTypeGroup::class)
                            ->setConstructorArgs([['id' => $contentTypeGroupId]])
                            ->getMockForAbstractClass(),
                    ]
                )
            );

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('getContentTypeService')
            ->will($this->returnValue($contentTypeServiceMock));

        return $repository;
    }
}

class_alias(ContentTypeGroupTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\Id\ContentTypeGroupTest');
