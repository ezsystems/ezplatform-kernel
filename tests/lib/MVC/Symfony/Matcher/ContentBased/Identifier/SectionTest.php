<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\Identifier;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\Section as SectionIdentifierMatcher;
use Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\BaseTest;

class SectionTest extends BaseTest
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\Section */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new SectionIdentifierMatcher();
    }

    /**
     * Returns a Repository mock configured to return the appropriate Section object with given section identifier.
     *
     * @param string $sectionIdentifier
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function generateRepositoryMockForSectionIdentifier($sectionIdentifier)
    {
        $sectionServiceMock = $this->createMock(SectionService::class);
        $sectionServiceMock->expects($this->once())
            ->method('loadSection')
            ->will(
                $this->returnValue(
                    $this
                        ->getMockBuilder(Section::class)
                        ->setConstructorArgs(
                            [
                                ['identifier' => $sectionIdentifier],
                            ]
                        )
                        ->getMockForAbstractClass()
                )
            );

        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->once())
            ->method('getSectionService')
            ->will($this->returnValue($sectionServiceMock));
        $repository
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->will($this->returnValue($this->getPermissionResolverMock()));

        return $repository;
    }

    /**
     * @dataProvider matchSectionProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\Section::matchLocation
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

        $location = $this->getLocationMock();
        $location
            ->expects($this->once())
            ->method('getContentInfo')
            ->will(
                $this->returnValue(
                    $this->getContentInfoMock(['sectionId' => 1])
                )
            );

        $this->assertSame(
            $expectedResult,
            $this->matcher->matchLocation($location)
        );
    }

    public function matchSectionProvider()
    {
        return [
            [
                'foo',
                $this->generateRepositoryMockForSectionIdentifier('foo'),
                true,
            ],
            [
                'foo',
                $this->generateRepositoryMockForSectionIdentifier('bar'),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->generateRepositoryMockForSectionIdentifier('bar'),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->generateRepositoryMockForSectionIdentifier('baz'),
                true,
            ],
        ];
    }

    /**
     * @dataProvider matchSectionProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier\Section::matchLocation
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
            $this->matcher->matchContentInfo($this->getContentInfoMock(['sectionId' => 1]))
        );
    }
}

class_alias(SectionTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\Identifier\SectionTest');
