<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Limitation;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo as SPIContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as SPIContentTypeHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Limitation\ParentContentTypeLimitationType;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\Location;

/**
 * Test Case for LimitationType.
 */
class ParentContentTypeLimitationTypeTest extends Base
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $locationHandlerMock;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Type\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeHandlerMock;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $contentHandlerMock;

    /**
     * Setup Location Handler mock.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->locationHandlerMock = $this->createMock(SPILocation\Handler::class);
        $this->contentTypeHandlerMock = $this->createMock(SPIContentTypeHandler::class);
        $this->contentHandlerMock = $this->createMock(SPIContentHandler::class);
    }

    /**
     * Tear down Location Handler mock.
     */
    protected function tearDown(): void
    {
        unset($this->locationHandlerMock);
        unset($this->contentTypeHandlerMock);
        unset($this->contentHandlerMock);
        parent::tearDown();
    }

    /**
     * @return \Ibexa\Core\Limitation\ParentContentTypeLimitationType
     */
    public function testConstruct()
    {
        return new ParentContentTypeLimitationType($this->getPersistenceMock());
    }

    /**
     * @return array
     */
    public function providerForTestAcceptValue()
    {
        return [
            [new ParentContentTypeLimitation()],
            [new ParentContentTypeLimitation([])],
            [new ParentContentTypeLimitation(['limitationValues' => ['', 'true', '2', 's3fd4af32r']])],
        ];
    }

    /**
     * @dataProvider providerForTestAcceptValue
     * @depends testConstruct
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation $limitation
     * @param \Ibexa\Core\Limitation\ParentContentTypeLimitationType $limitationType
     */
    public function testAcceptValue(ParentContentTypeLimitation $limitation, ParentContentTypeLimitationType $limitationType)
    {
        $limitationType->acceptValue($limitation);
    }

    /**
     * @return array
     */
    public function providerForTestAcceptValueException()
    {
        return [
            [new ObjectStateLimitation()],
            [new ParentContentTypeLimitation(['limitationValues' => [true]])],
            [new ParentContentTypeLimitation(['limitationValues' => [new \DateTime()]])],
        ];
    }

    /**
     * @dataProvider providerForTestAcceptValueException
     * @depends testConstruct
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     * @param \Ibexa\Core\Limitation\ParentContentTypeLimitationType $limitationType
     */
    public function testAcceptValueException(Limitation $limitation, ParentContentTypeLimitationType $limitationType)
    {
        $this->expectException(InvalidArgumentException::class);

        $limitationType->acceptValue($limitation);
    }

    /**
     * @return array
     */
    public function providerForTestValidatePass()
    {
        return [
            [new ParentContentTypeLimitation()],
            [new ParentContentTypeLimitation([])],
            [new ParentContentTypeLimitation(['limitationValues' => ['1']])],
        ];
    }

    /**
     * @dataProvider providerForTestValidatePass
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation $limitation
     */
    public function testValidatePass(ParentContentTypeLimitation $limitation)
    {
        if (!empty($limitation->limitationValues)) {
            $this->getPersistenceMock()
                ->expects($this->any())
                ->method('contentTypeHandler')
                ->will($this->returnValue($this->contentTypeHandlerMock));

            foreach ($limitation->limitationValues as $key => $value) {
                $this->contentTypeHandlerMock
                    ->expects($this->at($key))
                    ->method('load')
                    ->with($value)
                    ->will($this->returnValue(42));
            }
        }

        // Need to create inline instead of depending on testConstruct() to get correct mock instance
        $limitationType = $this->testConstruct();

        $validationErrors = $limitationType->validate($limitation);
        self::assertEmpty($validationErrors);
    }

    /**
     * @return array
     */
    public function providerForTestValidateError()
    {
        return [
            [new ParentContentTypeLimitation(), 0],
            [new ParentContentTypeLimitation(['limitationValues' => ['/1/777/']]), 1],
            [new ParentContentTypeLimitation(['limitationValues' => ['/1/888/', '/1/999/']]), 2],
        ];
    }

    /**
     * @dataProvider providerForTestValidateError
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation $limitation
     * @param int $errorCount
     */
    public function testValidateError(ParentContentTypeLimitation $limitation, $errorCount)
    {
        if (!empty($limitation->limitationValues)) {
            $this->getPersistenceMock()
                ->expects($this->any())
                ->method('contentTypeHandler')
                ->will($this->returnValue($this->contentTypeHandlerMock));

            foreach ($limitation->limitationValues as $key => $value) {
                $this->contentTypeHandlerMock
                    ->expects($this->at($key))
                    ->method('load')
                    ->with($value)
                    ->will($this->throwException(new NotFoundException('location', $value)));
            }
        } else {
            $this->getPersistenceMock()
                ->expects($this->never())
                ->method($this->anything());
        }

        // Need to create inline instead of depending on testConstruct() to get correct mock instance
        $limitationType = $this->testConstruct();

        $validationErrors = $limitationType->validate($limitation);
        self::assertCount($errorCount, $validationErrors);
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\ParentContentTypeLimitationType $limitationType
     */
    public function testBuildValue(ParentContentTypeLimitationType $limitationType)
    {
        $expected = ['test', 'test' => '1'];
        $value = $limitationType->buildValue($expected);

        self::assertInstanceOf(ParentContentTypeLimitation::class, $value);
        self::assertIsArray($value->limitationValues);
        self::assertEquals($expected, $value->limitationValues);
    }

    protected function getTestEvaluateContentMock()
    {
        $contentMock = $this->createMock(APIContent::class);

        $contentMock
            ->expects($this->once())
            ->method('getVersionInfo')
            ->will($this->returnValue($this->getTestEvaluateVersionInfoMock()));

        return $contentMock;
    }

    protected function getTestEvaluateVersionInfoMock()
    {
        $versionInfoMock = $this->createMock(APIVersionInfo::class);

        $versionInfoMock
            ->expects($this->once())
            ->method('getContentInfo')
            ->will($this->returnValue(new ContentInfo(['published' => true])));

        return $versionInfoMock;
    }

    /**
     * @return array
     */
    public function providerForTestEvaluate()
    {
        return [
            // ContentInfo, with API targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(),
                'object' => new ContentInfo(['published' => true]),
                'targets' => [new Location(['contentInfo' => new ContentInfo(['contentTypeId' => 24])])],
                'persistence' => [],
                'expected' => false,
            ],
            // ContentInfo, with SPI targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(),
                'object' => new ContentInfo(['published' => true]),
                'targets' => [new SPILocation(['contentId' => 42])],
                'persistence' => [
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '24'])],
                ],
                'expected' => false,
            ],
            // ContentInfo, with API targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentInfo(['published' => true]),
                'targets' => [new Location(['contentInfo' => new ContentInfo(['contentTypeId' => 24])])],
                'persistence' => [],
                'expected' => false,
            ],
            // ContentInfo, with SPI targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentInfo(['published' => true]),
                'targets' => [new SPILocation(['contentId' => 42])],
                'persistence' => [
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '24'])],
                ],
                'expected' => false,
            ],
            // ContentInfo, with API targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentInfo(['published' => true]),
                'targets' => [new Location(['contentInfo' => new ContentInfo(['contentTypeId' => 42])])],
                'persistence' => [],
                'expected' => true,
            ],
            // ContentInfo, with SPI targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentInfo(['published' => true]),
                'targets' => [new SPILocation(['contentId' => 24])],
                'persistence' => [
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => true,
            ],
            // ContentInfo, no targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [43]]),
                'object' => new ContentInfo(['published' => true, 'id' => 40]),
                'targets' => [],
                'persistence' => [
                    'locations' => [new SPILocation(['id' => 40, 'contentId' => '24', 'parentId' => 43, 'depth' => 1])],
                    'parentLocations' => [43 => new SPILocation(['id' => 43, 'contentId' => 24])],
                    'parentContents' => [24 => new SPIContentInfo(['id' => 24, 'contentTypeId' => 43])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => true,
            ],
            // ContentInfo, no targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [40]]),
                'object' => new ContentInfo(['published' => true, 'id' => 40]),
                'targets' => [],
                'persistence' => [
                    'locations' => [new SPILocation(['id' => 40, 'contentId' => '24', 'parentId' => 43, 'depth' => 1])],
                    'parentLocations' => [43 => new SPILocation(['id' => 43, 'contentId' => 24])],
                    'parentContents' => [24 => new SPIContentInfo(['id' => 24, 'contentTypeId' => 39])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => false,
            ],
            // ContentInfo, no targets, un-published, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentInfo(['published' => false]),
                'targets' => [],
                'persistence' => [
                    'locations' => [new SPILocation(['contentId' => '24'])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => true,
            ],
            // ContentInfo, no targets, un-published, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentInfo(['published' => false]),
                'targets' => [],
                'persistence' => [
                    'locations' => [new SPILocation(['contentId' => '24'])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '4200'])],
                ],
                'expected' => false,
            ],
            // Content, with API targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => $this->getTestEvaluateContentMock(),
                'targets' => [new Location(['contentInfo' => new ContentInfo(['contentTypeId' => 42])])],
                'persistence' => [],
                'expected' => true,
            ],
            // Content, with SPI targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => $this->getTestEvaluateContentMock(),
                'targets' => [new SPILocation(['contentId' => '24'])],
                'persistence' => [
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => true,
            ],
            // VersionInfo, with API targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => $this->getTestEvaluateVersionInfoMock(),
                'targets' => [new Location(['contentInfo' => new ContentInfo(['contentTypeId' => 42])])],
                'persistence' => [],
                'expected' => true,
            ],
            // VersionInfo, with SPI targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => $this->getTestEvaluateVersionInfoMock(),
                'targets' => [new SPILocation(['contentId' => '24'])],
                'persistence' => [
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => true,
            ],
            // VersionInfo, with LocationCreateStruct targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => $this->getTestEvaluateVersionInfoMock(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 24])],
                'persistence' => [
                    'locations' => [new SPILocation(['contentId' => 100])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '42'])],
                ],
                'expected' => true,
            ],
            // Content, with LocationCreateStruct targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => $this->getTestEvaluateContentMock(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 24])],
                'persistence' => [
                    'locations' => [new SPILocation(['contentId' => 100])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => '24'])],
                ],
                'expected' => false,
            ],
            // ContentCreateStruct, no targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [42]]),
                'object' => new ContentCreateStruct(),
                'targets' => [],
                'persistence' => [],
                'expected' => false,
            ],
            // ContentCreateStruct, with LocationCreateStruct targets, no access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [12, 23]]),
                'object' => new ContentCreateStruct(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 24])],
                'persistence' => [
                    'locations' => [new SPILocation(['contentId' => 100])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => 34])],
                ],
                'expected' => false,
            ],
            // ContentCreateStruct, with LocationCreateStruct targets, with access
            [
                'limitation' => new ParentContentTypeLimitation(['limitationValues' => [12, 23]]),
                'object' => new ContentCreateStruct(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 43])],
                'persistence' => [
                    'locations' => [new SPILocation(['contentId' => 100])],
                    'contentInfos' => [new SPIContentInfo(['contentTypeId' => 12])],
                ],
                'expected' => true,
            ],
        ];
    }

    protected function assertContentHandlerExpectations($callNo, $persistenceCalled, $contentId, $contentInfo)
    {
        $this->getPersistenceMock()
            ->expects($this->at($callNo + ($persistenceCalled ? 1 : 0)))
            ->method('contentHandler')
            ->will($this->returnValue($this->contentHandlerMock));

        $this->contentHandlerMock
            ->expects($this->at($callNo))
            ->method('loadContentInfo')
            ->with($contentId)
            ->will($this->returnValue($contentInfo));
    }

    /**
     * @dataProvider providerForTestEvaluate
     */
    public function testEvaluate(
        ParentContentTypeLimitation $limitation,
        ValueObject $object,
        $targets,
        array $persistence,
        $expected
    ) {
        // Need to create inline instead of depending on testConstruct() to get correct mock instance
        $limitationType = $this->testConstruct();

        $userMock = $this->getUserMock();
        $userMock
            ->expects($this->never())
            ->method($this->anything());

        $persistenceMock = $this->getPersistenceMock();
        // ContentTypeHandler is never used in evaluate()
        $persistenceMock
            ->expects($this->never())
            ->method('contentTypeHandler');

        if (empty($persistence)) {
            // Covers API targets, where no additional loading is required
            $persistenceMock
                ->expects($this->never())
                ->method($this->anything());
        } elseif (!empty($targets)) {
            foreach ($targets as $index => $target) {
                if ($target instanceof LocationCreateStruct) {
                    $this->getPersistenceMock()
                        ->expects($this->once($index))
                        ->method('locationHandler')
                        ->will($this->returnValue($this->locationHandlerMock));

                    $this->locationHandlerMock
                        ->expects($this->at($index))
                        ->method('load')
                        ->with($target->parentLocationId)
                        ->will($this->returnValue($location = $persistence['locations'][$index]));

                    $contentId = $location->contentId;
                } else {
                    $contentId = $target->contentId;
                }

                $this->assertContentHandlerExpectations(
                    $index,
                    $target instanceof LocationCreateStruct,
                    $contentId,
                    $persistence['contentInfos'][$index]
                );
            }
        } else {
            $this->getPersistenceMock()
                ->method('locationHandler')
                ->will($this->returnValue($this->locationHandlerMock));

            $this->getPersistenceMock()
                ->method('contentHandler')
                ->will($this->returnValue($this->contentHandlerMock));

            $this->locationHandlerMock
                ->method(
                    $object instanceof ContentInfo && $object->published ? 'loadLocationsByContent' : 'loadParentLocationsForDraftContent'
                )
                ->with($object->id)
                ->will($this->returnValue($persistence['locations']));

            foreach ($persistence['locations'] as $location) {
                if (!empty($persistence['parentLocations'][$location->parentId])) {
                    $this->locationHandlerMock
                            ->method('load')
                            ->with($location->parentId)
                            ->will($this->returnValue($persistence['parentLocations'][$location->parentId]));
                }

                if (!empty($persistence['parentLocations'][$location->parentId])) {
                    $this->contentHandlerMock
                            ->method('loadContentInfo')
                            ->with($location->contentId)
                            ->willReturn($persistence['parentContents'][$location->contentId]);
                }
            }

            foreach ($persistence['locations'] as $index => $location) {
                $this->assertContentHandlerExpectations(
                    $index,
                    true,
                    $location->contentId,
                    $persistence['contentInfos'][$index]
                );
            }
        }

        $value = $limitationType->evaluate(
            $limitation,
            $userMock,
            $object,
            $targets
        );

        self::assertIsBool($value);
        self::assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public function providerForTestEvaluateInvalidArgument()
    {
        return [
            // invalid limitation
            [
                'limitation' => new ObjectStateLimitation(),
                'object' => new ContentInfo(),
                'targets' => [new Location()],
                'persistence' => [],
            ],
            // invalid object
            [
                'limitation' => new ParentContentTypeLimitation(),
                'object' => new ObjectStateLimitation(),
                'targets' => [],
                'persistence' => [],
            ],
            // invalid target when using ContentCreateStruct
            [
                'limitation' => new ParentContentTypeLimitation(),
                'object' => new ContentCreateStruct(),
                'targets' => [new Location()],
                'persistence' => [],
            ],
            // invalid target when not using ContentCreateStruct
            [
                'limitation' => new ParentContentTypeLimitation(),
                'object' => new ContentInfo(),
                'targets' => [new ObjectStateLimitation()],
                'persistence' => [],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestEvaluateInvalidArgument
     */
    public function testEvaluateInvalidArgument(Limitation $limitation, ValueObject $object, $targets)
    {
        $this->expectException(InvalidArgumentException::class);

        // Need to create inline instead of depending on testConstruct() to get correct mock instance
        $limitationType = $this->testConstruct();

        $userMock = $this->getUserMock();
        $userMock
            ->expects($this->never())
            ->method($this->anything());

        $persistenceMock = $this->getPersistenceMock();
        $persistenceMock
            ->expects($this->never())
            ->method($this->anything());

        $limitationType->evaluate(
            $limitation,
            $userMock,
            $object,
            $targets
        );
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\ParentContentTypeLimitationType $limitationType
     */
    public function testGetCriterionInvalidValue(ParentContentTypeLimitationType $limitationType)
    {
        $this->expectException(NotImplementedException::class);

        $limitationType->getCriterion(
            new ParentContentTypeLimitation([]),
            $this->getUserMock()
        );
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\ParentContentTypeLimitationType $limitationType
     */
    public function testValueSchema(ParentContentTypeLimitationType $limitationType)
    {
        $this->markTestIncomplete('Method is not implemented yet: ' . __METHOD__);
    }
}

class_alias(ParentContentTypeLimitationTypeTest::class, 'eZ\Publish\Core\Limitation\Tests\ParentContentTypeLimitationTypeTest');
