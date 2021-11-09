<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\ContentTypeService as APIService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use Ibexa\Core\Repository\SiteAccessAware\ContentTypeService;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\User\User;

class ContentTypeServiceTest extends AbstractServiceTest
{
    public function getAPIServiceClassName()
    {
        return APIService::class;
    }

    public function getSiteAccessAwareServiceClassName()
    {
        return ContentTypeService::class;
    }

    public function providerForPassTroughMethods()
    {
        $contentTypeGroupCreateStruct = new ContentTypeGroupCreateStruct();
        $contentTypeGroupUpdateStruct = new ContentTypeGroupUpdateStruct();
        $contentTypeGroup = new ContentTypeGroup();

        $contentTypeCreateStruct = new ContentTypeCreateStruct();
        $contentTypeUpdateStruct = new ContentTypeUpdateStruct();
        $contentType = new ContentType();
        $contentTypeDraft = new ContentTypeDraft();

        $fieldDefinition = new FieldDefinition();
        $fieldDefinitionCreateStruct = new FieldDefinitionCreateStruct();
        $fieldDefinitionUpdateStruct = new FieldDefinitionUpdateStruct();

        $user = new User();

        // string $method, array $arguments, bool $return = true
        return [
            ['createContentTypeGroup', [$contentTypeGroupCreateStruct], $contentTypeGroup],

            ['updateContentTypeGroup', [$contentTypeGroup, $contentTypeGroupUpdateStruct], null],

            ['deleteContentTypeGroup', [$contentTypeGroup], null],

            ['createContentType', [$contentTypeCreateStruct, [$contentTypeGroup]], $contentTypeDraft],

            ['loadContentTypeDraft', [22], $contentTypeDraft],

            ['createContentTypeDraft', [$contentType], $contentTypeDraft],

            ['updateContentTypeDraft', [$contentTypeDraft, $contentTypeUpdateStruct], null],

            ['deleteContentType', [$contentType], null],

            ['copyContentType', [$contentType], $contentType],
            ['copyContentType', [$contentType, $user], $contentType],

            ['assignContentTypeGroup', [$contentType, $contentTypeGroup], null],

            ['unassignContentTypeGroup', [$contentType, $contentTypeGroup], null],

            ['addFieldDefinition', [$contentTypeDraft, $fieldDefinitionCreateStruct], null],

            ['removeFieldDefinition', [$contentTypeDraft, $fieldDefinition], null],

            ['updateFieldDefinition', [$contentTypeDraft, $fieldDefinition, $fieldDefinitionUpdateStruct], null],

            ['publishContentTypeDraft', [$contentTypeDraft], null],

            ['newContentTypeGroupCreateStruct', ['media'], $contentTypeGroupCreateStruct],

            ['newContentTypeCreateStruct', ['blog'], $contentTypeCreateStruct],

            ['newContentTypeUpdateStruct', [], $contentTypeUpdateStruct],

            ['newContentTypeGroupUpdateStruct', [], $contentTypeGroupUpdateStruct],

            ['newFieldDefinitionCreateStruct', ['body', 'ezstring'], $fieldDefinitionCreateStruct],

            ['newFieldDefinitionUpdateStruct', [], $fieldDefinitionUpdateStruct],

            ['isContentTypeUsed', [$contentType], true],

            ['removeContentTypeTranslation', [$contentTypeDraft, 'ger-DE'], $contentTypeDraft],

            ['deleteUserDrafts', [14], null],
        ];
    }

    public function providerForLanguagesLookupMethods()
    {
        $contentType = new ContentType();
        $contentTypeGroup = new ContentTypeGroup();

        // string $method, array $arguments, bool $return, int $languageArgumentIndex
        return [
            ['loadContentTypeGroup', [33, self::LANG_ARG], $contentTypeGroup, 1],

            ['loadContentTypeGroupByIdentifier', ['content', self::LANG_ARG], $contentTypeGroup, 1],

            ['loadContentTypeGroups', [self::LANG_ARG], [$contentTypeGroup], 0],

            ['loadContentType', [22, self::LANG_ARG], $contentType, 1],

            ['loadContentTypeList', [[22, self::LANG_ARG]], [$contentType], 1],

            ['loadContentTypeByIdentifier', ['article', self::LANG_ARG], $contentType, 1],

            ['loadContentTypeByRemoteId', ['w4ini3tn4f', self::LANG_ARG], $contentType, 1],

            ['loadContentTypes', [$contentTypeGroup, self::LANG_ARG], [$contentType], 1],
        ];
    }
}

class_alias(ContentTypeServiceTest::class, 'eZ\Publish\Core\Repository\SiteAccessAware\Tests\ContentTypeServiceTest');
