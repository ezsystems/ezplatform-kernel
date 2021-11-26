<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\ContentService as APIService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentDraftList;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentList;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Core\Repository\SiteAccessAware\ContentService;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\User\User;

/**
 * @property \Ibexa\Contracts\Core\Repository\ContentService $service
 */
class ContentServiceTest extends AbstractServiceTest
{
    public function getAPIServiceClassName()
    {
        return APIService::class;
    }

    public function getSiteAccessAwareServiceClassName()
    {
        return ContentService::class;
    }

    public function providerForPassTroughMethods(): array
    {
        $contentInfo = new ContentInfo();
        $versionInfo = new VersionInfo();
        $content = $this->createMock(Content::class);
        $relation = $this->createMock(Relation::class);
        $relationList = new RelationList();
        $contentCreateStruct = new ContentCreateStruct();
        $contentUpdateStruct = new ContentUpdateStruct();
        $contentMetaStruct = new ContentMetadataUpdateStruct();
        $locationCreateStruct = new LocationCreateStruct();
        $user = new User();
        $contentType = new ContentType();
        $language = new Language();

        // string $method, array $arguments, bool $return = true
        return [
            ['loadContentInfo', [42], $contentInfo],
            ['loadContentInfoList', [[42]], [$contentInfo]],

            ['loadContentInfoByRemoteId', ['f348tj4gorgji4'], $contentInfo],

            ['loadVersionInfo', [$contentInfo], $versionInfo],
            ['loadVersionInfo', [$contentInfo, 3], $versionInfo],

            ['loadVersionInfoById', [42], $versionInfo],
            ['loadVersionInfoById', [42, 3], $versionInfo],

            ['createContent', [$contentCreateStruct], $content],
            ['createContent', [$contentCreateStruct, [44]], $content],

            ['updateContentMetadata', [$contentInfo, $contentMetaStruct], $content],

            ['deleteContent', [$contentInfo], null],

            ['createContentDraft', [$contentInfo], $content],
            ['createContentDraft', [$contentInfo, $versionInfo], $content],
            ['createContentDraft', [$contentInfo, $versionInfo, $user], $content],
            ['createContentDraft', [$contentInfo, $versionInfo, $user, $language], $content],

            ['countContentDrafts', [], 0],
            ['countContentDrafts', [$user], 0],

            ['loadContentDrafts', [], [$content]],
            ['loadContentDrafts', [$user], [$content]],

            ['loadContentDraftList', [], new ContentDraftList()],
            ['loadContentDraftList', [$user], new ContentDraftList()],
            ['loadContentDraftList', [$user, 1], new ContentDraftList()],
            ['loadContentDraftList', [$user, 1, 25], new ContentDraftList()],

            ['updateContent', [$versionInfo, $contentUpdateStruct], $content],

            ['publishVersion', [$versionInfo], $content],

            ['deleteVersion', [$versionInfo], null],

            ['loadVersions', [$contentInfo], [$versionInfo]],

            ['copyContent', [$contentInfo, $locationCreateStruct], $content],
            ['copyContent', [$contentInfo, $locationCreateStruct, $versionInfo], $content],

            ['loadRelations', [$versionInfo], [$relation]],

            ['countReverseRelations', [$contentInfo], 0],

            ['loadReverseRelations', [$contentInfo], $relationList],
            ['loadReverseRelationList', [$contentInfo], $relationList],

            ['addRelation', [$versionInfo, $contentInfo], null],

            ['deleteRelation', [$versionInfo, $contentInfo], null],

            ['deleteTranslation', [$contentInfo, 'eng-GB'], null],

            ['deleteTranslationFromDraft', [$versionInfo, 'eng-GB'], $content],

            ['hideContent', [$contentInfo], null],
            ['revealContent', [$contentInfo], null],

            ['newContentCreateStruct', [$contentType, 'eng-GB'], $contentCreateStruct],
            ['newContentMetadataUpdateStruct', [], $contentMetaStruct],
            ['newContentUpdateStruct', [], $contentUpdateStruct],
            ['validate', [$contentUpdateStruct, []], []],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function providerForLanguagesLookupMethods(): array
    {
        $content = $this->createMock(Content::class);
        $contentInfo = new ContentInfo();
        $versionInfo = new VersionInfo();

        $filter = new Filter(new ContentId(1));

        // string $method, array $arguments, bool $return, int $languageArgumentIndex
        return [
            ['loadContentByContentInfo', [$contentInfo], $content, 1],
            ['loadContentByContentInfo', [$contentInfo, self::LANG_ARG, 4, false], $content, 1],

            ['loadContentByVersionInfo', [$versionInfo], $content, 1],
            ['loadContentByVersionInfo', [$versionInfo, self::LANG_ARG, false], $content, 1],

            ['loadContent', [42], $content, 1],
            ['loadContent', [42, self::LANG_ARG, 4, false], $content, 1],

            ['loadContentByRemoteId', ['f348tj4gorgji4'], $content, 1],
            ['loadContentByRemoteId', ['f348tj4gorgji4', self::LANG_ARG, 4, false], $content, 1],

            ['loadContentListByContentInfo', [[$contentInfo]], [], 1],
            ['loadContentListByContentInfo', [[$contentInfo], self::LANG_ARG, false], [], 1],

            ['find', [$filter], new ContentList(1, [$content]), 1],
            ['find', [$filter, self::LANG_ARG], new ContentList(1, [$content]), 1],
        ];
    }
}

class_alias(ContentServiceTest::class, 'eZ\Publish\Core\Repository\SiteAccessAware\Tests\ContentServiceTest');
