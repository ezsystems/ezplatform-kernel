<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\SortClause;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

final class ScoreTest extends AbstractSortClauseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $searchService = $this->getRepository()->getSearchService();
        if (!$searchService->supports(SearchService::CAPABILITY_SCORING)) {
            self::markTestSkipped("Search engine doesn't support scoring");
        }
    }

    /**
     * @param string[] $values
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     *
     * @dataProvider dataProviderForTestSortingByScore
     */
    public function testSortingByScore(iterable $inputValues, Query $query, array $expectedOrderedIds): void
    {
        $this->createContentForScoreSortTesting($inputValues);

        $searchService = $this->getRepository()->getSearchService();
        if ($query instanceof LocationQuery) {
            $actualResults = $searchService->findLocations($query);
        } else {
            $actualResults = $searchService->findContentInfo($query);
        }

        $this->assertSearchResultOrderByRemoteId($expectedOrderedIds, $actualResults);
    }

    public function dataProviderForTestSortingByScore(): iterable
    {
        // The following input values for test content guarantee predictable scoring
        $inputValues = ['foo foo', 'foo', 'foo foo foo'];

        yield 'content asc' => [
            $inputValues,
            new Query([
                'query' => new Criterion\FullText('foo'),
                'sortClauses' => [
                    new SortClause\Score(Query::SORT_ASC),
                    new SortClause\ContentId(),
                ],
            ]),
            ['foo', 'foo foo', 'foo foo foo'],
        ];

        yield 'content desc' => [
            $inputValues,
            new Query([
                'query' => new Criterion\FullText('foo'),
                'sortClauses' => [
                    new SortClause\Score(Query::SORT_DESC),
                    new SortClause\ContentId(),
                ],
            ]),
            ['foo foo foo', 'foo foo', 'foo'],
        ];

        yield 'location asc' => [
            $inputValues,
            new LocationQuery([
                'query' => new Criterion\FullText('foo'),
                'sortClauses' => [
                    new SortClause\Score(Query::SORT_ASC),
                    new SortClause\ContentId(),
                ],
            ]),
            ['foo', 'foo foo', 'foo foo foo'],
        ];

        yield 'location desc' => [
            $inputValues,
            new LocationQuery([
                'query' => new Criterion\FullText('foo'),
                'sortClauses' => [
                    new SortClause\Score(Query::SORT_DESC),
                    new SortClause\ContentId(),
                ],
            ]),
            ['foo foo foo', 'foo foo', 'foo'],
        ];
    }

    /**
     * @param string[] $values
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    private function createContentForScoreSortTesting(iterable $values): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('score_sort_test');
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'score_sort_test'];
        $contentTypeCreateStruct->creatorId = 14;
        $contentTypeCreateStruct->creationDate = new \DateTime();

        $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('value', 'ezstring');
        $fieldCreate->names = ['eng-GB' => 'value'];
        $fieldCreate->fieldGroup = 'main';
        $fieldCreate->position = 1;

        $contentTypeCreateStruct->addFieldDefinition($fieldCreate);

        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentGroup]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);

        foreach ($values as $value) {
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $contentCreateStruct->remoteId = $value;
            $contentCreateStruct->alwaysAvailable = false;
            $contentCreateStruct->setField('value', $value);

            $locationCreateStruct = $locationService->newLocationCreateStruct(2);
            $locationCreateStruct->remoteId = $value;

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

class_alias(ScoreTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\SortClause\ScoreTest');
