<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Regression;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

/**
 * @see https://jira.ez.no/browse/EZP-28799
 */
class EZP28799SubtreeSearchTest extends BaseTest
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    public function createTestContent()
    {
        $rootLocationId = 2;
        $contentService = $this->getRepository()->getContentService();
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $locationService = $this->getRepository()->getLocationService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $locationCreateStruct = $locationService->newLocationCreateStruct($rootLocationId);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField('name', 'LEFT');

        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $leftFolder = $contentService->publishVersion($draft->versionInfo);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField('name', 'RIGHT');

        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $rightFolder = $contentService->publishVersion($draft->versionInfo);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField('name', 'TARGET');

        $locationCreateStructLeft = $locationService->newLocationCreateStruct(
            $leftFolder->contentInfo->mainLocationId
        );
        $locationCreateStructRight = $locationService->newLocationCreateStruct(
            $rightFolder->contentInfo->mainLocationId
        );
        $draft = $contentService->createContent(
            $contentCreateStruct,
            [
                $locationCreateStructLeft,
                $locationCreateStructRight,
            ]
        );
        $targetFolder = $contentService->publishVersion($draft->versionInfo);

        return [$leftFolder, $rightFolder, $targetFolder];
    }

    public function testConflictingConditions()
    {
        list($leftFolder, $rightFolder, $targetFolder) = $this->createTestContent();
        $locationService = $this->getRepository()->getLocationService();
        $leftLocation = $locationService->loadLocation($leftFolder->contentInfo->mainLocationId);

        $query = new Query([
            'filter' => new Query\Criterion\LogicalAnd([
                new Query\Criterion\ContentId($targetFolder->contentInfo->id),
                new Query\Criterion\Subtree($leftLocation->pathString),
                new Query\Criterion\LogicalNot(
                    new Query\Criterion\Subtree($leftLocation->pathString)
                ),
            ]),
        ]);

        $searchService = $this->getRepository()->getSearchService();
        $result = $searchService->findContent($query);

        $this->assertSame(0, $result->totalCount);
    }

    public function testNegativeSubtree()
    {
        list($leftFolder, $rightFolder, $targetFolder) = $this->createTestContent();
        $locationService = $this->getRepository()->getLocationService();
        $leftLocation = $locationService->loadLocation($leftFolder->contentInfo->mainLocationId);

        $query = new Query([
            'filter' => new Query\Criterion\LogicalAnd([
                new Query\Criterion\ContentId($targetFolder->contentInfo->id),
                new Query\Criterion\LogicalNot(
                    new Query\Criterion\Subtree($leftLocation->pathString)
                ),
            ]),
        ]);

        $searchService = $this->getRepository()->getSearchService();
        $result = $searchService->findContent($query);

        $this->assertSame(0, $result->totalCount);
    }
}

class_alias(EZP28799SubtreeSearchTest::class, 'eZ\Publish\API\Repository\Tests\Regression\EZP28799SubtreeSearchTest');
