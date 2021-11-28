<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\ContentService;

use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\Repository\Validator\VersionValidator;
use Ibexa\Core\Repository\Values\Content\TrashItem;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

/**
 * @internal
 */
final class VersionValidatorTest extends BaseTest
{
    private const CONTENT_TYPE_IDENTIFIER = 'single-text';
    private const FIELD_IDENTIFIER = 'name';
    private const ENG_GB = 'eng-GB';
    private const GER_DE = 'ger-DE';

    /** @var \Ibexa\Core\Repository\Validator\VersionValidator */
    private $validator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new VersionValidator(
            $this->createMock(FieldTypeRegistry::class)
        );

        $repository = $this->getRepository();

        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
    }

    public function testSupportsKnownValidationObject(): void
    {
        self::assertTrue($this->validator->supports(new VersionInfo()));
    }

    public function testSupportsUnknownValidationObject(): void
    {
        self::assertFalse($this->validator->supports(new TrashItem()));
    }

    public function testValidateMultilingualContent(): void
    {
        $contentType = $this->createSimpleContentType(
            self::CONTENT_TYPE_IDENTIFIER,
            self::ENG_GB,
            [
                self::FIELD_IDENTIFIER => 'ezstring',
            ],
        );

        $content = $this->createAndPublishMultilingualContent($contentType);
        $this->makeContentTypeFieldRequired($contentType);

        try {
            $this->updateContentTranslation($content);
        } catch (ContentFieldValidationException $e) {
            // since we updated only one translation, we expect one field error to be thrown (related to null value for eng-GB version)
            self::assertCount(1, $e->getFieldErrors());
        }
    }

    private function createAndPublishMultilingualContent(ContentType $contentType): Content
    {
        $contentCreate = $this->contentService->newContentCreateStruct($contentType, self::ENG_GB);
        $contentCreate->setField(self::FIELD_IDENTIFIER, null, self::ENG_GB);
        $contentCreate->setField(self::FIELD_IDENTIFIER, 'Name DE', self::GER_DE);

        $contentDraft = $this->contentService->createContent($contentCreate);

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    private function makeContentTypeFieldRequired(ContentType $contentType): void
    {
        $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
        $nameField = $contentTypeDraft->getFieldDefinition(self::FIELD_IDENTIFIER);

        $fieldDefinitionUpdate = $this->contentTypeService->newFieldDefinitionUpdateStruct();
        $fieldDefinitionUpdate->isRequired = true;

        $this->contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $nameField,
            $fieldDefinitionUpdate
        );

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function updateContentTranslation(Content $content): void
    {
        $contentDraft = $this->contentService->createContentDraft($content->contentInfo);
        $contentUpdate = $this->contentService->newContentUpdateStruct();
        $contentUpdate->setField(self::FIELD_IDENTIFIER, 'Updated name DE', self::GER_DE);

        $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdate);
        $this->contentService->publishVersion($contentDraft->versionInfo);
    }
}

class_alias(VersionValidatorTest::class, 'eZ\Publish\API\Repository\Tests\ContentService\VersionValidatorTest');
