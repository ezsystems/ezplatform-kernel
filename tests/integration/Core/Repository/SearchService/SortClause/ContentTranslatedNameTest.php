<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\SortClause;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

final class ContentTranslatedNameTest extends AbstractSortClauseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->isLegacySearchEngineSetup()) {
            self::markTestSkipped("Legacy search engine doesn't support ContentTranslatedName");
        }
    }

    /**
     * @param string[] $values
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     *
     * @dataProvider dataProviderForTestSortingByContentTranslatedName
     */
    public function testContentSortingByContentTranslatedName(
        iterable $inputValues,
        SortClause $sortClause,
        array $languageFilter,
        array $expectedOrderedRemoteIds
    ): void {
        $this->createContentForContentTranslatedNameTesting($inputValues);

        $query = new Query([
            'filter' => new Criterion\ContentTypeIdentifier('content_translated_name_test'),
            'sortClauses' => [
                $sortClause,
                new SortClause\ContentId(Query::SORT_ASC),
            ],
        ]);

        $searchService = $this->getRepository()->getSearchService();
        $actualResults = $searchService->findContentInfo($query, $languageFilter);

        $this->assertSearchResultOrderByRemoteId($expectedOrderedRemoteIds, $actualResults);
    }

    /**
     * @param string[] $values
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     *
     * @dataProvider dataProviderForTestSortingByContentTranslatedName
     */
    public function testLocationSortingByContentTranslatedName(
        iterable $inputValues,
        SortClause $sortClause,
        array $languageFilter,
        array $expectedOrderedRemoteIds
    ): void {
        $this->createContentForContentTranslatedNameTesting($inputValues);

        $query = new LocationQuery([
            'filter' => new Criterion\ContentTypeIdentifier('content_translated_name_test'),
            'sortClauses' => [
                $sortClause,
                new SortClause\ContentId(Query::SORT_ASC),
            ],
        ]);

        $searchService = $this->getRepository()->getSearchService();
        $actualResults = $searchService->findLocations($query, $languageFilter);

        $this->assertSearchResultOrderByRemoteId($expectedOrderedRemoteIds, $actualResults);
    }

    public function dataProviderForTestSortingByContentTranslatedName(): iterable
    {
        $inputValues = [
            'foo' => [
                'eng-GB' => 'A',
                'pol-PL' => 'C',
            ],
            'bar' => [
                'eng-GB' => 'B',
                'pol-PL' => 'B',
            ],
            'baz' => [
                'eng-GB' => 'C',
                'pol-PL' => 'A',
            ],
        ];

        yield 'eng-GB, ASC' => [
            $inputValues,
            new SortClause\ContentTranslatedName(Query::SORT_ASC),
            [
                'languages' => [
                    'eng-GB',
                ],
                'useAlwaysAvailable' => false,
            ],
            ['foo', 'bar', 'baz'],
        ];

        yield 'eng-GB, DESC' => [
            $inputValues,
            new SortClause\ContentTranslatedName(Query::SORT_DESC),
            [
                'languages' => [
                    'eng-GB',
                ],
                'useAlwaysAvailable' => false,
            ],
            ['baz', 'bar', 'foo'],
        ];

        yield 'pol-PL, ASC' => [
            $inputValues,
            new SortClause\ContentTranslatedName(Query::SORT_ASC),
            [
                'languages' => [
                    'pol-PL',
                ],
                'useAlwaysAvailable' => false,
            ],
            ['baz', 'bar', 'foo'],
        ];

        yield 'pol-PL, DESC' => [
            $inputValues,
            new SortClause\ContentTranslatedName(Query::SORT_DESC),
            [
                'languages' => [
                    'pol-PL',
                ],
                'useAlwaysAvailable' => false,
            ],
            ['foo', 'bar', 'baz'],
        ];

        // Content name from main translation should be used ("C")
        unset($inputValues['baz']['pol-PL']);

        yield 'eng-GB + pol-PL, ASC' => [
            $inputValues,
            new SortClause\ContentTranslatedName(Query::SORT_ASC),
            [
                'languages' => [
                    'pol-PL', 'eng-GB',
                ],
                'useAlwaysAvailable' => true,
            ],
            ['bar', 'foo', 'baz'],
        ];

        yield 'eng-GB + pol-PL, DESC' => [
            $inputValues,
            new SortClause\ContentTranslatedName(Query::SORT_DESC),
            [
                'languages' => [
                    'pol-PL', 'eng-GB',
                ],
                'useAlwaysAvailable' => true,
            ],
            ['foo', 'baz', 'bar'],
        ];
    }

    private function createContentForContentTranslatedNameTesting(iterable $values): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('content_translated_name_test');
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'content_translated_name_test'];
        $contentTypeCreateStruct->creatorId = 14;
        $contentTypeCreateStruct->creationDate = new DateTime();
        $contentTypeCreateStruct->nameSchema = '<value>';
        $contentTypeCreateStruct->defaultAlwaysAvailable = true;

        $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('value', 'ezstring');
        $fieldCreate->names = ['eng-GB' => 'value'];
        $fieldCreate->fieldGroup = 'main';
        $fieldCreate->position = 1;

        $contentTypeCreateStruct->addFieldDefinition($fieldCreate);

        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentGroup]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);

        foreach ($values as $remoteId => $translations) {
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $contentCreateStruct->remoteId = $remoteId;
            $contentCreateStruct->alwaysAvailable = false;
            foreach ($translations as $languageCode => $value) {
                $this->createLanguageIfNotExists($languageCode, $languageCode);

                $contentCreateStruct->setField('value', $value, $languageCode);
            }

            $locationCreateStruct = $locationService->newLocationCreateStruct(2);
            $locationCreateStruct->remoteId = $remoteId;

            $draft = $contentService->createContent(
                $contentCreateStruct,
                [
                    $locationCreateStruct,
                ]
            );

            $contentService->publishVersion($draft->getVersionInfo());
        }

        $this->refreshSearch($repository);
    }
}

class_alias(ContentTranslatedNameTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\SortClause\ContentTranslatedNameTest');
