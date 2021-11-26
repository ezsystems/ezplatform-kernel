<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Limitation;

use Ibexa\Contracts\Core\Persistence\Content\VersionInfo as SPIVersionInfo;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\StatusLimitation;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Limitation\StatusLimitationType;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\User;

/**
 * Test Case for LimitationType.
 */
class StatusLimitationTypeTest extends Base
{
    /**
     * @return \Ibexa\Core\Limitation\StatusLimitationType
     */
    public function testConstruct()
    {
        return new StatusLimitationType();
    }

    /**
     * @return array
     */
    public function providerForTestAcceptValue()
    {
        return [
            [new StatusLimitation()],
            [new StatusLimitation([])],
            [
                new StatusLimitation(
                    [
                        'limitationValues' => [
                            VersionInfo::STATUS_DRAFT,
                            VersionInfo::STATUS_PUBLISHED,
                            VersionInfo::STATUS_ARCHIVED,
                        ],
                    ]
                ),
            ],
        ];
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestAcceptValue
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\StatusLimitation $limitation
     * @param \Ibexa\Core\Limitation\StatusLimitationType $limitationType
     */
    public function testAcceptValue(StatusLimitation $limitation, StatusLimitationType $limitationType)
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
            [new StatusLimitation(['limitationValues' => [true]])],
        ];
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestAcceptValueException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     * @param \Ibexa\Core\Limitation\StatusLimitationType $limitationType
     */
    public function testAcceptValueException(Limitation $limitation, StatusLimitationType $limitationType)
    {
        $this->expectException(InvalidArgumentException::class);

        $limitationType->acceptValue($limitation);
    }

    /**
     * @return array
     */
    public function providerForTestValidateError()
    {
        return [
            [new StatusLimitation(), 0],
            [new StatusLimitation([]), 0],
            [
                new StatusLimitation(
                    [
                        'limitationValues' => [SPIVersionInfo::STATUS_PUBLISHED],
                    ]
                ),
                0,
            ],
            [new StatusLimitation(['limitationValues' => [100]]), 1],
            [
                new StatusLimitation(
                    [
                        'limitationValues' => [
                            SPIVersionInfo::STATUS_PUBLISHED,
                            PHP_INT_MAX,
                        ],
                    ]
                ),
                1,
            ],
            [
                new StatusLimitation(
                    [
                        'limitationValues' => [
                            SPIVersionInfo::STATUS_PENDING,
                            SPIVersionInfo::STATUS_REJECTED,
                        ],
                    ]
                ),
                2,
            ],
            [
                new StatusLimitation(
                    [
                        'limitationValues' => [
                            SPIVersionInfo::STATUS_DRAFT,
                            SPIVersionInfo::STATUS_PUBLISHED,
                            SPIVersionInfo::STATUS_ARCHIVED,
                        ],
                    ]
                ),
                0,
            ],
        ];
    }

    /**
     * @dataProvider providerForTestValidateError
     * @depends testConstruct
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\StatusLimitation $limitation
     * @param int $errorCount
     * @param \Ibexa\Core\Limitation\StatusLimitationType $limitationType
     */
    public function testValidateError(StatusLimitation $limitation, $errorCount, StatusLimitationType $limitationType)
    {
        $validationErrors = $limitationType->validate($limitation);
        self::assertCount($errorCount, $validationErrors);
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\StatusLimitationType $limitationType
     */
    public function testBuildValue(StatusLimitationType $limitationType)
    {
        $expected = ['test', 'test' => 9];
        $value = $limitationType->buildValue($expected);

        self::assertInstanceOf(StatusLimitation::class, $value);
        self::assertIsArray($value->limitationValues);
        self::assertEquals($expected, $value->limitationValues);
    }

    protected function getVersionInfoMock($shouldBeCalled = true)
    {
        $versionInfoMock = $this->getMockBuilder(APIVersionInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['__get'])
            ->getMockForAbstractClass();

        if ($shouldBeCalled) {
            $versionInfoMock
                ->expects($this->once())
                ->method('__get')
                ->with('status')
                ->will($this->returnValue(24));
        } else {
            $versionInfoMock
                ->expects($this->never())
                ->method('__get')
                ->with('status');
        }

        return $versionInfoMock;
    }

    protected function getContentMock()
    {
        $contentMock = $this->getMockBuilder(APIContent::class)
            ->setConstructorArgs([])
            ->setMethods([])
            ->getMock();

        $contentMock
            ->expects($this->once())
            ->method('getVersionInfo')
            ->will($this->returnValue($this->getVersionInfoMock()));

        return $contentMock;
    }

    /**
     * @return array
     */
    public function providerForTestEvaluate()
    {
        return [
            // VersionInfo, no access
            [
                'limitation' => new StatusLimitation(),
                'object' => $this->getVersionInfoMock(false),
                'expected' => false,
            ],
            // VersionInfo, no access
            [
                'limitation' => new StatusLimitation(['limitationValues' => [42]]),
                'object' => $this->getVersionInfoMock(),
                'expected' => false,
            ],
            // VersionInfo, with access
            [
                'limitation' => new StatusLimitation(['limitationValues' => [24]]),
                'object' => $this->getVersionInfoMock(),
                'expected' => true,
            ],
            // Content, no access
            [
                'limitation' => new StatusLimitation(),
                'object' => $this->getContentMock(),
                'expected' => false,
            ],
            // Content, no access
            [
                'limitation' => new StatusLimitation(['limitationValues' => [42]]),
                'object' => $this->getContentMock(),
                'expected' => false,
            ],
            // Content, with access
            [
                'limitation' => new StatusLimitation(['limitationValues' => [24]]),
                'object' => $this->getContentMock(),
                'expected' => true,
            ],
        ];
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestEvaluate
     */
    public function testEvaluate(
        StatusLimitation $limitation,
        ValueObject $object,
        $expected,
        StatusLimitationType $limitationType
    ) {
        $userMock = $this->getUserMock();
        $userMock->expects($this->never())
            ->method($this->anything());

        $userMock = new User();
        $value = $limitationType->evaluate(
            $limitation,
            $userMock,
            $object
        );

        self::assertIsBool($value);
        self::assertEquals($expected, $value);
    }

    /**
     * @return array
     */
    public function providerForTestEvaluateInvalidArgument()
    {
        $versionInfoMock = $this->getMockBuilder(APIVersionInfo::class)
            ->setConstructorArgs([])
            ->setMethods([])
            ->getMock();

        return [
            // invalid limitation
            [
                'limitation' => new ObjectStateLimitation(),
                'object' => $versionInfoMock,
            ],
            // invalid object
            [
                'limitation' => new StatusLimitation(),
                'object' => new ObjectStateLimitation(),
            ],
        ];
    }

    /**
     * @depends testConstruct
     * @dataProvider providerForTestEvaluateInvalidArgument
     */
    public function testEvaluateInvalidArgument(
        Limitation $limitation,
        ValueObject $object,
        StatusLimitationType $limitationType
    ) {
        $this->expectException(InvalidArgumentException::class);

        $userMock = $this->getUserMock();
        $userMock->expects($this->never())->method($this->anything());

        $userMock = new User();
        $limitationType->evaluate(
            $limitation,
            $userMock,
            $object
        );
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\StatusLimitationType $limitationType
     */
    public function testGetCriterion(StatusLimitationType $limitationType)
    {
        $this->expectException(NotImplementedException::class);

        $limitationType->getCriterion(new StatusLimitation(), $this->getUserMock());
    }

    /**
     * @depends testConstruct
     *
     * @param \Ibexa\Core\Limitation\StatusLimitationType $limitationType
     */
    public function testValueSchema(StatusLimitationType $limitationType)
    {
        self::markTestSkipped('Method valueSchema() is not implemented');
    }
}

class_alias(StatusLimitationTypeTest::class, 'eZ\Publish\Core\Limitation\Tests\StatusLimitationTypeTest');
