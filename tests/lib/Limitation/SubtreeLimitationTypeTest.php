<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as LimitationType;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as SPILocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Limitation\SubtreeLimitationType;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\Query\Criterion\PermissionSubtree;

/**
 * Test Case for LimitationType.
 */
class SubtreeLimitationTypeTest extends Base
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $locationHandlerMock;

    /**
     * Setup Location Handler mock.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->locationHandlerMock = $this->createMock(SPILocationHandler::class);
    }

    /**
     * Tear down Location Handler mock.
     */
    protected function tearDown(): void
    {
        unset($this->locationHandlerMock);
        parent::tearDown();
    }

    /**
     * @return \Ibexa\Core\Limitation\SubtreeLimitationType
     */
    public function testConstruct()
    {
        return new SubtreeLimitationType($this->getPersistenceMock());
    }

    /**
     * @return array
     */
    public function providerForTestAcceptValue()
    {
        return [
            [new SubtreeLimitation()],
            [new SubtreeLimitation([])],
            [new SubtreeLimitation(['limitationValues' => ['', 'true', '2', 's3fdaf32r']])],
        ];
    }

    /**
     * @dataProvider providerForTestAcceptValue
     * @depends testConstruct
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation $limitation
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testAcceptValue(SubtreeLimitation $limitation, SubtreeLimitationType $limitationType)
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
            [new SubtreeLimitation(['limitationValues' => [true]])],
            [new SubtreeLimitation(['limitationValues' => [1]])],
            [new SubtreeLimitation(['limitationValues' => [0]])],
            [new SubtreeLimitation(['limitationValues' => '/1/2/'])],
        ];
    }

    /**
     * @dataProvider providerForTestAcceptValueException
     * @depends testConstruct
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testAcceptValueException(Limitation $limitation, SubtreeLimitationType $limitationType)
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
            [new SubtreeLimitation()],
            [new SubtreeLimitation([])],
            [new SubtreeLimitation(['limitationValues' => ['/1/2/']])],
        ];
    }

    /**
     * @dataProvider providerForTestValidatePass
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation $limitation
     */
    public function testValidatePass(SubtreeLimitation $limitation)
    {
        if (!empty($limitation->limitationValues)) {
            $this->getPersistenceMock()
                ->expects($this->any())
                ->method('locationHandler')
                ->will($this->returnValue($this->locationHandlerMock));

            foreach ($limitation->limitationValues as $key => $value) {
                $pathArray = explode('/', trim($value, '/'));
                $this->locationHandlerMock
                    ->expects($this->at($key))
                    ->method('load')
                    ->with(end($pathArray))
                    ->will(
                        $this->returnValue(
                            new SPILocation(['pathString' => $value])
                        )
                    );
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
            [new SubtreeLimitation(), 0],
            [new SubtreeLimitation(['limitationValues' => ['/1/777/']]), 1],
            [new SubtreeLimitation(['limitationValues' => ['/1/888/', '/1/999/']]), 2],
        ];
    }

    /**
     * @dataProvider providerForTestValidateError
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation $limitation
     * @param int $errorCount
     */
    public function testValidateError(SubtreeLimitation $limitation, $errorCount)
    {
        if (!empty($limitation->limitationValues)) {
            $this->getPersistenceMock()
                ->expects($this->any())
                ->method('locationHandler')
                ->will($this->returnValue($this->locationHandlerMock));

            foreach ($limitation->limitationValues as $key => $value) {
                $pathArray = explode('/', trim($value, '/'));
                $this->locationHandlerMock
                    ->expects($this->at($key))
                    ->method('load')
                    ->with(end($pathArray))
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

    public function testValidateErrorWrongPath()
    {
        $limitation = new SubtreeLimitation(['limitationValues' => ['/1/2/42/']]);

        $this->getPersistenceMock()
            ->expects($this->any())
            ->method('locationHandler')
            ->will($this->returnValue($this->locationHandlerMock));

        foreach ($limitation->limitationValues as $key => $value) {
            $pathArray = explode('/', trim($value, '/'));
            $this->locationHandlerMock
                ->expects($this->at($key))
                ->method('load')
                ->with(end($pathArray))
                ->will(
                    $this->returnValue(
                        new SPILocation(['pathString' => '/1/5/42'])
                    )
                );
        }

        // Need to create inline instead of depending on testConstruct() to get correct mock instance
        $limitationType = $this->testConstruct();

        $validationErrors = $limitationType->validate($limitation);
        self::assertCount(1, $validationErrors);
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testBuildValue(SubtreeLimitationType $limitationType)
    {
        $expected = ['test', 'test' => '/1/999/'];
        $value = $limitationType->buildValue($expected);

        self::assertInstanceOf(SubtreeLimitation::class, $value);
        self::assertIsArray($value->limitationValues);
        self::assertEquals($expected, $value->limitationValues);
    }

    /**
     * @return array
     */
    public function providerForTestEvaluate()
    {
        // Mocks for testing Content & VersionInfo objects, should only be used once because of expect rules.
        $contentMock = $this->createMock(APIContent::class);
        $versionInfoMock = $this->createMock(APIVersionInfo::class);

        $contentMock
            ->expects($this->once())
            ->method('getVersionInfo')
            ->will($this->returnValue($versionInfoMock));

        $versionInfoMock
            ->expects($this->once())
            ->method('getContentInfo')
            ->willReturn(new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]));

        $versionInfoMock2 = $this->createMock(APIVersionInfo::class);

        $versionInfoMock2
            ->expects($this->once())
            ->method('getContentInfo')
            ->willReturn(new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]));

        return [
            // ContentInfo, with targets, no access
            [
                'limitation' => new SubtreeLimitation(),
                'object' => new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]),
                'targets' => [new Location()],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_DENIED,
            ],
            // ContentInfo, with targets, no access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]),
                'targets' => [new Location(['pathString' => '/1/55/'])],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_DENIED,
            ],
            // ContentInfo, with targets, with access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]),
                'targets' => [new Location(['pathString' => '/1/2/'])],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_GRANTED,
            ],
            // ContentInfo, no targets, with access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]),
                'targets' => null,
                'persistence' => [new Location(['pathString' => '/1/2/'])],
                'expected' => LimitationType::ACCESS_GRANTED,
            ],
            // ContentInfo, no targets, no access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/', '/1/43/']]),
                'object' => new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]),
                'targets' => null,
                'persistence' => [new Location(['pathString' => '/1/55/'])],
                'expected' => LimitationType::ACCESS_DENIED,
            ],
            // ContentInfo, no targets, un-published, with access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => new ContentInfo(['published' => false, 'status' => ContentInfo::STATUS_DRAFT]),
                'targets' => null,
                'persistence' => [new Location(['pathString' => '/1/2/'])],
                'expected' => LimitationType::ACCESS_GRANTED,
            ],
            // ContentInfo, no targets, un-published, no access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/', '/1/43/']]),
                'object' => new ContentInfo(['published' => false, 'status' => ContentInfo::STATUS_DRAFT]),
                'targets' => null,
                'persistence' => [new Location(['pathString' => '/1/55/'])],
                'expected' => LimitationType::ACCESS_DENIED,
            ],
            // Content, with targets, with access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => $contentMock,
                'targets' => [new Location(['pathString' => '/1/2/'])],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_GRANTED,
            ],
            // VersionInfo, with targets, with access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => $versionInfoMock2,
                'targets' => [new Location(['pathString' => '/1/2/'])],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_GRANTED,
            ],
            // ContentCreateStruct, no targets, no access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'object' => new ContentCreateStruct(),
                'targets' => [],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_DENIED,
            ],
            // ContentCreateStruct, with targets, no access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/', '/1/43/']]),
                'object' => new ContentCreateStruct(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 55])],
                'persistence' => [new Location(['pathString' => '/1/55/'])],
                'expected' => LimitationType::ACCESS_DENIED,
            ],
            // ContentCreateStruct, with targets, with access
            [
                'limitation' => new SubtreeLimitation(['limitationValues' => ['/1/2/', '/1/43/']]),
                'object' => new ContentCreateStruct(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 43])],
                'persistence' => [new Location(['pathString' => '/1/43/'])],
                'expected' => LimitationType::ACCESS_GRANTED,
            ],
            // invalid object
            [
                'limitation' => new SubtreeLimitation(),
                'object' => new ObjectStateLimitation(),
                'targets' => [new LocationCreateStruct(['parentLocationId' => 43])],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_ABSTAIN,
            ],
            // invalid target
            [
                'limitation' => new SubtreeLimitation(),
                'object' => new ContentInfo(['published' => true, 'status' => ContentInfo::STATUS_PUBLISHED]),
                'targets' => [new ObjectStateLimitation()],
                'persistence' => [],
                'expected' => LimitationType::ACCESS_ABSTAIN,
            ],
        ];
    }

    /**
     * @dataProvider providerForTestEvaluate
     */
    public function testEvaluate(
        SubtreeLimitation $limitation,
        ValueObject $object,
        $targets,
        array $persistenceLocations,
        $expected
    ) {
        // Need to create inline instead of depending on testConstruct() to get correct mock instance
        $limitationType = $this->testConstruct();

        $userMock = $this->getUserMock();
        $userMock
            ->expects($this->never())
            ->method($this->anything());

        $persistenceMock = $this->getPersistenceMock();
        if (empty($persistenceLocations) && $targets !== null) {
            $persistenceMock
                ->expects($this->never())
                ->method($this->anything());
        } elseif ($object instanceof ContentCreateStruct) {
            foreach ((array)$targets as $key => $target) {
                $this->getPersistenceMock()
                    ->expects($this->at($key))
                    ->method('locationHandler')
                    ->will($this->returnValue($this->locationHandlerMock));

                $this->locationHandlerMock
                    ->expects($this->at($key))
                    ->method('load')
                    ->with($target->parentLocationId)
                    ->will($this->returnValue($persistenceLocations[$key]));
            }
        } else {
            $this->getPersistenceMock()
                ->expects($this->once())
                ->method('locationHandler')
                ->will($this->returnValue($this->locationHandlerMock));

            $this->locationHandlerMock
                ->expects($this->once())
                ->method($object instanceof ContentInfo && $object->published ? 'loadLocationsByContent' : 'loadParentLocationsForDraftContent')
                ->with($object->id)
                ->will($this->returnValue($persistenceLocations));
        }

        $value = $limitationType->evaluate(
            $limitation,
            $userMock,
            $object,
            $targets
        );

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
            // invalid target when using ContentCreateStruct
            [
                'limitation' => new SubtreeLimitation(),
                'object' => new ContentCreateStruct(),
                'targets' => [new Location()],
                'persistence' => [],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestEvaluateInvalidArgument
     */
    public function testEvaluateInvalidArgument(
        Limitation $limitation,
        ValueObject $object,
        $targets,
        array $persistenceLocations
    ) {
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

        $v = $limitationType->evaluate(
            $limitation,
            $userMock,
            $object,
            $targets
        );
        var_dump($v); // intentional, debug in case no exception above
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testGetCriterionInvalidValue(SubtreeLimitationType $limitationType)
    {
        $this->expectException(\RuntimeException::class);

        $limitationType->getCriterion(
            new SubtreeLimitation([]),
            $this->getUserMock()
        );
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testGetCriterionSingleValue(SubtreeLimitationType $limitationType)
    {
        $criterion = $limitationType->getCriterion(
            new SubtreeLimitation(['limitationValues' => ['/1/9/']]),
            $this->getUserMock()
        );

        // Assert that $criterion is instance of API type (for Solr/ES), and internal type(optimization for SQL engines)
        self::assertInstanceOf(Subtree::class, $criterion);
        self::assertInstanceOf(PermissionSubtree::class, $criterion);
        self::assertIsArray($criterion->value);
        self::assertIsString($criterion->operator);
        self::assertEquals(Operator::EQ, $criterion->operator);
        self::assertEquals(['/1/9/'], $criterion->value);
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testGetCriterionMultipleValues(SubtreeLimitationType $limitationType)
    {
        $criterion = $limitationType->getCriterion(
            new SubtreeLimitation(['limitationValues' => ['/1/9/', '/1/55/']]),
            $this->getUserMock()
        );

        // Assert that $criterion is instance of API type (for Solr/ES), and internal type(optimization for SQL engines)
        self::assertInstanceOf(Subtree::class, $criterion);
        self::assertInstanceOf(PermissionSubtree::class, $criterion);
        self::assertIsArray($criterion->value);
        self::assertIsString($criterion->operator);
        self::assertEquals(Operator::IN, $criterion->operator);
        self::assertEquals(['/1/9/', '/1/55/'], $criterion->value);
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\SubtreeLimitationType $limitationType
     */
    public function testValueSchema(SubtreeLimitationType $limitationType)
    {
        self::assertEquals(
            SubtreeLimitationType::VALUE_SCHEMA_LOCATION_PATH,
            $limitationType->valueSchema()
        );
    }
}

class_alias(SubtreeLimitationTypeTest::class, 'eZ\Publish\Core\Limitation\Tests\SubtreeLimitationTypeTest');
