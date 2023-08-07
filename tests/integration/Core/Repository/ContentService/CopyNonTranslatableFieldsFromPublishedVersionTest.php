<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\ContentService;

use DateTime;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Tests\Integration\Core\RepositoryTestCase;

/**
 * @covers \eZ\Publish\API\Repository\ContentService
 */
final class CopyNonTranslatableFieldsFromPublishedVersionTest extends RepositoryTestCase
{
    private const GER_DE = 'ger-DE';
    private const ENG_US = 'eng-US';
    private const CONTENT_TYPE_IDENTIFIER = 'nontranslatable';
    private const TEXT_LINE_FIELD_TYPE_IDENTIFIER = 'ezstring';

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function testCopyNonTranslatableFieldsFromPublishedVersionToDraft(): void
    {
        $this->createNonTranslatableContentType();

        $contentService = self::getContentService();
        $contentTypeService = self::getContentTypeService();
        $locationService = self::getLocationService();

        // Creating start content in eng-US language
        $contentType = $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $mainLanguageCode = self::ENG_US;
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, $mainLanguageCode);
        $contentCreateStruct->setField('title', 'Test title');

        $contentDraft = $contentService->createContent(
            $contentCreateStruct,
            [
                $locationService->newLocationCreateStruct(2),
            ]
        );
        $publishedContent = $contentService->publishVersion($contentDraft->getVersionInfo());

        // Creating a draft in ger-DE language with the only field updated being 'title'
        $gerDraft = $contentService->createContentDraft($publishedContent->contentInfo);

        $contentUpdateStruct = new ContentUpdateStruct([
            'initialLanguageCode' => self::GER_DE,
            'fields' => $contentDraft->getFields(),
        ]);

        $contentUpdateStruct->setField('title', 'Folder GER', self::GER_DE);
        $gerContent = $contentService->updateContent($gerDraft->getVersionInfo(), $contentUpdateStruct);

        // Updating non-translatable field in eng-US language (allowed) and publishing it
        $engContent = $contentService->createContentDraft($publishedContent->contentInfo);

        $contentUpdateStruct = new ContentUpdateStruct([
            'initialLanguageCode' => self::ENG_US,
            'fields' => $contentDraft->getFields(),
        ]);

        $expectedBodyValue = 'Nontranslatable value';
        $contentUpdateStruct->setField('title', 'Title v2', self::ENG_US);
        $contentUpdateStruct->setField('body', $expectedBodyValue, self::ENG_US);

        $engContent = $contentService->updateContent($engContent->getVersionInfo(), $contentUpdateStruct);
        $contentService->publishVersion($engContent->getVersionInfo());

        // Publishing ger-DE draft with the empty non-translatable field
        $contentService->publishVersion($gerContent->getVersionInfo());

        // Loading main content
        $mainPublishedContent = $contentService->loadContent($engContent->id);
        $bodyFieldValue = $mainPublishedContent->getField('body')->getValue();

        self::assertSame($expectedBodyValue, $bodyFieldValue->text);
    }

    private function createNonTranslatableContentType(): void
    {
        $permissionResolver = self::getPermissionResolver();
        $contentTypeService = self::getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct(self::CONTENT_TYPE_IDENTIFIER);

        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->remoteId = '1234567890abcdef';
        $typeCreate->urlAliasSchema = '<title>';
        $typeCreate->nameSchema = '<title>';
        $typeCreate->names = [
            'eng-GB' => 'Nontranslatable content type',
        ];
        $typeCreate->descriptions = [
            'eng-GB' => '',
        ];
        $typeCreate->creatorId = $permissionResolver->getCurrentUserReference()->getUserId();
        $typeCreate->creationDate = new DateTime();

        $fieldDefinitionPosition = 1;
        $typeCreate->addFieldDefinition(
            $this->buildFieldDefinitionCreateStructForNonTranslatableContentType(
                $fieldDefinitionPosition,
                'title',
                ['eng-GB' => 'Title'],
                true,
                true,
                'default title'
            )
        );

        $typeCreate->addFieldDefinition(
            $this->buildFieldDefinitionCreateStructForNonTranslatableContentType(
                ++$fieldDefinitionPosition,
                'body',
                ['eng-GB' => 'Body'],
                false,
                false
            )
        );

        $contentTypeDraft = $contentTypeService->createContentType(
            $typeCreate,
            [$contentTypeService->loadContentTypeGroupByIdentifier('Media')],
        );
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    /**
     * @param array<string, string> $names
     */
    private function buildFieldDefinitionCreateStructForNonTranslatableContentType(
        int $position,
        string $fieldIdentifier,
        array $names,
        bool $isTranslatable,
        bool $isRequired,
        ?string $defaultValue = null
    ): FieldDefinitionCreateStruct {
        $contentTypeService = self::getContentTypeService();

        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
            $fieldIdentifier,
            self::TEXT_LINE_FIELD_TYPE_IDENTIFIER
        );

        $fieldDefinitionCreateStruct->names = $names;
        $fieldDefinitionCreateStruct->descriptions = $names;
        $fieldDefinitionCreateStruct->fieldGroup = 'content';
        $fieldDefinitionCreateStruct->position = $position;
        $fieldDefinitionCreateStruct->isTranslatable = $isTranslatable;
        $fieldDefinitionCreateStruct->isRequired = $isRequired;
        $fieldDefinitionCreateStruct->isInfoCollector = false;
        $fieldDefinitionCreateStruct->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $fieldDefinitionCreateStruct->fieldSettings = [];
        $fieldDefinitionCreateStruct->isSearchable = true;
        $fieldDefinitionCreateStruct->defaultValue = $defaultValue;

        return $fieldDefinitionCreateStruct;
    }
}
