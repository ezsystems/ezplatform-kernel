<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\ContentService;

use Datetime;
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

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $titleFieldCreate->names = [
            'eng-GB' => 'Title',
        ];
        $titleFieldCreate->descriptions = [
            'eng-GB' => 'Title',
        ];
        $titleFieldCreate->fieldGroup = 'content';
        $titleFieldCreate->position = 1;
        $titleFieldCreate->isTranslatable = true;
        $titleFieldCreate->isRequired = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $titleFieldCreate->fieldSettings = [];
        $titleFieldCreate->isSearchable = true;
        $titleFieldCreate->defaultValue = 'default title';

        $typeCreate->addFieldDefinition($titleFieldCreate);

        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('body', 'ezstring');
        $bodyFieldCreate->names = [
            'eng-GB' => 'Body',
        ];
        $bodyFieldCreate->descriptions = [
            'eng-GB' => 'Body',
        ];
        $bodyFieldCreate->fieldGroup = 'content';
        $bodyFieldCreate->position = 2;
        $bodyFieldCreate->isTranslatable = false;
        $bodyFieldCreate->isRequired = false;
        $bodyFieldCreate->isInfoCollector = false;
        $bodyFieldCreate->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $bodyFieldCreate->fieldSettings = [];
        $bodyFieldCreate->isSearchable = true;
        $bodyFieldCreate->defaultValue = null;

        $typeCreate->addFieldDefinition($bodyFieldCreate);

        $contentTypeDraft = $contentTypeService->createContentType(
            $typeCreate,
            [$contentTypeService->loadContentTypeGroupByIdentifier('Media')],
        );
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }
}
