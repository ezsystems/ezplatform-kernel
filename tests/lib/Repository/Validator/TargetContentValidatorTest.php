<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\Persistence\Content;
use Ibexa\Core\Repository\Validator\TargetContentValidator;
use PHPUnit\Framework\TestCase;

final class TargetContentValidatorTest extends TestCase
{
    /** @var \eZ\Publish\SPI\Persistence\Content\Handler|\PHPUnit_Framework_MockObject_MockObject */
    private $contentHandler;

    /** @var \eZ\Publish\SPI\Persistence\Content\Type\Handler|\PHPUnit_Framework_MockObject_MockObject */
    private $contentTypeHandler;

    /** @var \Ibexa\Core\Repository\Validator\TargetContentValidator */
    private $targetContentValidator;

    public function setUp(): void
    {
        $this->contentHandler = $this->createMock(Content\Handler::class);
        $this->contentTypeHandler = $this->createMock(Content\Type\Handler::class);

        $this->targetContentValidator = new TargetContentValidator($this->contentHandler, $this->contentTypeHandler);
    }

    public function testValidateWithValidContent(): void
    {
        $contentId = 2;
        $allowedContentTypes = ['article'];

        $this->setupContentTypeValidation($contentId);

        $validationError = $this->targetContentValidator->validate($contentId, $allowedContentTypes);

        self::assertNull($validationError);
    }

    public function testValidateWithInvalidContentType(): void
    {
        $contentId = 2;
        $allowedContentTypes = ['folder'];

        $this->setupContentTypeValidation($contentId);

        $validationError = $this->targetContentValidator->validate($contentId, $allowedContentTypes);

        self::assertInstanceOf(ValidationError::class, $validationError);
    }

    private function setupContentTypeValidation(int $contentId): void
    {
        $contentTypeId = 55;
        $contentInfo = new Content\ContentInfo(['contentTypeId' => $contentTypeId]);
        $versionInfo = new Content\VersionInfo(['contentInfo' => $contentInfo]);
        $content = new Content(['versionInfo' => $versionInfo]);
        $contentType = new Content\Type(['id' => $contentTypeId, 'identifier' => 'article']);

        $this->contentHandler
            ->expects(self::once())
            ->method('load')
            ->with($contentId)
            ->willReturn($content);

        $this->contentTypeHandler
            ->expects(self::once())
            ->method('load')
            ->with($contentInfo->contentTypeId)
            ->willReturn($contentType);
    }

    public function testValidateWithInvalidContentId(): void
    {
        $id = 0;

        $this->contentHandler
            ->method('load')
            ->with($id)
            ->willThrowException($this->createMock(NotFoundException::class));

        $validationError = $this->targetContentValidator->validate($id);

        self::assertInstanceOf(ValidationError::class, $validationError);
    }
}
