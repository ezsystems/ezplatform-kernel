<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\URL\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult;

/**
 * Base class for URLService tests.
 */
abstract class BaseURLServiceTest extends BaseTest
{
    private const URL_CONTENT_TYPE_IDENTIFIER = 'link_ct';

    protected function doTestFindUrls(URLQuery $query, array $expectedUrls, ?int $expectedTotalCount, bool $ignoreOrder = true)
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $searchResult = $repository->getURLService()->findUrls($query);
        /* END: Use Case */

        $this->assertInstanceOf(SearchResult::class, $searchResult);
        $this->assertSame($expectedTotalCount, $searchResult->totalCount);
        $this->assertSearchResultItems($searchResult, $expectedUrls, $ignoreOrder);
    }

    protected function assertSearchResultItems(SearchResult $searchResult, array $expectedUrls, $ignoreOrder)
    {
        $this->assertCount(count($expectedUrls), $searchResult->items);

        foreach ($searchResult->items as $i => $item) {
            if ($ignoreOrder) {
                $this->assertContains($item->url, $expectedUrls);
            } else {
                $this->assertEquals($expectedUrls[$i], $item->url);
            }
        }
    }

    protected function assertSearchResultItemsAreUnique(SearchResult $results): void
    {
        $visitedUrls = [];

        foreach ($results->items as $item) {
            $this->assertNotContains(
                $item->url,
                $visitedUrls,
                'Search results contains duplicated url: ' . $item->url
            );

            $visitedUrls[] = $item->url;
        }
    }

    protected function assertUsagesSearchResultItems(UsageSearchResult $searchResult, array $expectedContentInfoIds)
    {
        $this->assertCount(count($expectedContentInfoIds), $searchResult->items);
        foreach ($searchResult->items as $contentInfo) {
            $this->assertContains($contentInfo->id, $expectedContentInfoIds);
        }
    }

    protected function createContentWithLink(
        string $name,
        string $url,
        string $languageCode = 'eng-GB',
        int $parentLocationId = 2
    ): Content {
        $repository = $this->getRepository(false);
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $locationService = $repository->getLocationService();

        try {
            $contentType = $contentTypeService->loadContentTypeByIdentifier(self::URL_CONTENT_TYPE_IDENTIFIER);
        } catch (NotFoundException $e) {
            $contentType = $this->createContentTypeWithUrl();
        }

        $struct = $contentService->newContentCreateStruct($contentType, $languageCode);
        $struct->setField('name', $name, $languageCode);
        $struct->setField('url', $url, $languageCode);

        $contentDraft = $contentService->createContent(
            $struct,
            [$locationService->newLocationCreateStruct($parentLocationId)]
        );

        return $contentService->publishVersion($contentDraft->versionInfo);
    }

    private function createContentTypeWithUrl(): ContentType
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct(self::URL_CONTENT_TYPE_IDENTIFIER);
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->urlAliasSchema = 'url|scheme';
        $typeCreate->nameSchema = 'name|scheme';
        $typeCreate->names = [
            'eng-GB' => 'URL: ' . self::URL_CONTENT_TYPE_IDENTIFIER,
        ];
        $typeCreate->descriptions = [
            'eng-GB' => '',
        ];
        $typeCreate->creatorId = $this->generateId('user', $repository->getPermissionResolver()->getCurrentUserReference()->getUserId());
        $typeCreate->creationDate = $this->createDateTime();

        $typeCreate->addFieldDefinition($this->createNameFieldDefinitionCreateStruct($contentTypeService));
        $typeCreate->addFieldDefinition($this->createUrlFieldDefinitionCreateStruct($contentTypeService));

        $contentTypeDraft = $contentTypeService->createContentType($typeCreate, [
            $contentTypeService->loadContentTypeGroupByIdentifier('Content'),
        ]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        return $contentTypeService->loadContentTypeByIdentifier(self::URL_CONTENT_TYPE_IDENTIFIER);
    }

    private function createNameFieldDefinitionCreateStruct(ContentTypeService $contentTypeService): FieldDefinitionCreateStruct
    {
        $nameFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('name', 'ezstring');
        $nameFieldCreate->names = [
            'eng-GB' => 'Name',
        ];
        $nameFieldCreate->descriptions = [
            'eng-GB' => '',
        ];
        $nameFieldCreate->fieldGroup = 'default';
        $nameFieldCreate->position = 1;
        $nameFieldCreate->isTranslatable = false;
        $nameFieldCreate->isRequired = true;
        $nameFieldCreate->isInfoCollector = false;
        $nameFieldCreate->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $nameFieldCreate->fieldSettings = [];
        $nameFieldCreate->isSearchable = true;
        $nameFieldCreate->defaultValue = '';

        return $nameFieldCreate;
    }

    private function createUrlFieldDefinitionCreateStruct(ContentTypeService $contentTypeService): FieldDefinitionCreateStruct
    {
        $urlFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('url', 'ezurl');
        $urlFieldCreate->names = [
            'eng-GB' => 'URL',
        ];
        $urlFieldCreate->descriptions = [
            'eng-GB' => '',
        ];
        $urlFieldCreate->fieldGroup = 'default';
        $urlFieldCreate->position = 2;
        $urlFieldCreate->isTranslatable = false;
        $urlFieldCreate->isRequired = true;
        $urlFieldCreate->isInfoCollector = false;
        $urlFieldCreate->validatorConfiguration = [];
        $urlFieldCreate->fieldSettings = [];
        $urlFieldCreate->isSearchable = false;
        $urlFieldCreate->defaultValue = '';

        return $urlFieldCreate;
    }
}

class_alias(BaseURLServiceTest::class, 'eZ\Publish\API\Repository\Tests\BaseURLServiceTest');
