<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\Identifier;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\ParentContentType as ParentContentTypeMatcher;
use Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\BaseTest;

class ParentContentTypeTest extends BaseTest
{
    private const EXAMPLE_LOCATION_ID = 54;
    private const EXAMPLE_PARENT_LOCATION_ID = 2;

    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\ParentContentType */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new ParentContentTypeMatcher();
    }

    /**
     * Returns a Repository mock configured to return the appropriate Section object with given section identifier.
     *
     * @param string $contentTypeIdentifier
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateRepositoryMockForContentTypeIdentifier($contentTypeIdentifier)
    {
        $parentContentInfo = $this->getContentInfoMock([
            'mainLocationId' => self::EXAMPLE_LOCATION_ID,
            'contentTypeId' => 42,
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

        $contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $contentTypeServiceMock->expects($this->once())
            ->method('loadContentType')
            ->with(42)
            ->will(
                $this->returnValue(
                    $this
                        ->getMockBuilder(ContentType::class)
                        ->setConstructorArgs(
                            [
                                ['identifier' => $contentTypeIdentifier],
                            ]
                        )
                        ->getMockForAbstractClass()
                )
            );

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($locationServiceMock));
        $repository
            ->expects($this->once())
            ->method('getContentTypeService')
            ->will($this->returnValue($contentTypeServiceMock));
        $repository
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->will($this->returnValue($this->getPermissionResolverMock()));

        return $repository;
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\ParentContentType::matchLocation
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
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
            $this->matcher->matchLocation($this->getLocationMock([
                'parentLocationId' => self::EXAMPLE_LOCATION_ID,
            ]))
        );
    }

    public function matchLocationProvider()
    {
        return [
            [
                'foo',
                $this->generateRepositoryMockForContentTypeIdentifier('foo'),
                true,
            ],
            [
                'foo',
                $this->generateRepositoryMockForContentTypeIdentifier('bar'),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->generateRepositoryMockForContentTypeIdentifier('bar'),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->generateRepositoryMockForContentTypeIdentifier('baz'),
                true,
            ],
        ];
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\ParentContentType::matchLocation
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     * @covers \Ibexa\Core\MVC\RepositoryAware::setRepository
     *
     * @param string|string[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
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

class_alias(ParentContentTypeTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\Identifier\ParentContentTypeTest');
