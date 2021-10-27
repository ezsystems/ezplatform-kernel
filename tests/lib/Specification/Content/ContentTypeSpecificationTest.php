<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Specification\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Specification\Content\ContentSpecification;
use Ibexa\Contracts\Core\Specification\Content\ContentTypeSpecification;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\Core\Specification\Content\ContentTypeSpecification
 */
final class ContentTypeSpecificationTest extends TestCase
{
    private const EXISTING_CONTENT_TYPE_IDENTIFIER = 'article';
    private const NOT_EXISTING_CONTENT_TYPE_IDENTIFIER = 'Some-Not-Existing-CT-Identifier';

    public function testConstructorWithExistingContentTypeIdentifier(): void
    {
        $contentTypeSpecification = new ContentTypeSpecification(
            self::EXISTING_CONTENT_TYPE_IDENTIFIER
        );

        $this->assertInstanceOf(ContentSpecification::class, $contentTypeSpecification);
    }

    public function testConstructorWithNotExistingContentTypeIdentifier(): void
    {
        $contentTypeSpecification = new ContentTypeSpecification(
            self::NOT_EXISTING_CONTENT_TYPE_IDENTIFIER
        );

        $this->assertInstanceOf(ContentSpecification::class, $contentTypeSpecification);
    }

    /**
     * @dataProvider providerForIsSatisfiedBy
     */
    public function testIsSatisfiedBy(
        string $contentTypeSpecificationIdentifier,
        string $contentTypeIdentifier,
        bool $shouldBeSatisfied
    ): void {
        $contentTypeSpecification = new ContentTypeSpecification(
            $contentTypeSpecificationIdentifier
        );

        $contentTypeMock = $this->getMockBuilder(ContentType::class)
            ->setConstructorArgs(
                [['identifier' => $contentTypeIdentifier]]
            )
            ->getMockForAbstractClass();

        $contentMock = $this->createMock(Content::class);
        $contentMock->expects($this->once())
            ->method('getContentType')
            ->willReturn($contentTypeMock);

        $this->assertEquals(
            $contentTypeSpecification->isSatisfiedBy($contentMock),
            $shouldBeSatisfied
        );
    }

    public function providerForIsSatisfiedBy(): array
    {
        return [
            [self::EXISTING_CONTENT_TYPE_IDENTIFIER, self::EXISTING_CONTENT_TYPE_IDENTIFIER, true],
            [self::NOT_EXISTING_CONTENT_TYPE_IDENTIFIER, self::EXISTING_CONTENT_TYPE_IDENTIFIER, false],
        ];
    }
}

class_alias(ContentTypeSpecificationTest::class, 'eZ\Publish\SPI\Specification\Tests\Content\ContentTypeSpecificationTest');
