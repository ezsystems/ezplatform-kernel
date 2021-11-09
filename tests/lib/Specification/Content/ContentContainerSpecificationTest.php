<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Specification\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Specification\Content\ContentContainerSpecification;
use Ibexa\Contracts\Core\Specification\Content\ContentSpecification;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\Core\Specification\Content\ContentContainerSpecification
 */
final class ContentContainerSpecificationTest extends TestCase
{
    public function testConstructor(): void
    {
        $contentTypeSpecification = new ContentContainerSpecification();

        $this->assertInstanceOf(ContentSpecification::class, $contentTypeSpecification);
    }

    /**
     * @dataProvider providerForIsSatisfiedBy
     */
    public function testIsSatisfiedBy(
        bool $isContainer,
        bool $shouldBeSatisfied
    ): void {
        $contentContainerSpecification = new ContentContainerSpecification();

        $contentTypeMock = $this->getMockBuilder(ContentType::class)
            ->setConstructorArgs(
                [['isContainer' => $isContainer]]
            )
            ->getMockForAbstractClass();

        $contentMock = $this->createMock(Content::class);
        $contentMock->expects($this->once())
            ->method('getContentType')
            ->willReturn($contentTypeMock);

        $this->assertEquals(
            $contentContainerSpecification->isSatisfiedBy($contentMock),
            $shouldBeSatisfied
        );
    }

    public function providerForIsSatisfiedBy(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}

class_alias(ContentContainerSpecificationTest::class, 'eZ\Publish\SPI\Specification\Tests\Content\ContentContainerSpecificationTest');
