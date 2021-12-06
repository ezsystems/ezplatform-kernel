<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Regression;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LanguageCode;
use Ibexa\Solr\LegacySetupFactory;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

/**
 * Test case for language issues in EZP-20018.
 *
 * Issue EZP-20018
 */
class EZP20018LanguageTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->getRepository();

        // Loaded services
        $contentService = $repository->getContentService();
        $languageService = $repository->getContentLanguageService();

        //Create Por-PT Language
        $langCreateStruct = $languageService->newLanguageCreateStruct();
        $langCreateStruct->languageCode = 'por-PT';
        $langCreateStruct->name = 'Portuguese (portuguese)';
        $langCreateStruct->enabled = true;

        $languageService->createLanguage($langCreateStruct);

        // Translate "Image" Folder name to por-PT
        $objUpdateStruct = $contentService->newContentUpdateStruct();
        $objUpdateStruct->initialLanguageCode = 'eng-US';
        $objUpdateStruct->setField('name', 'Imagens', 'por-PT');

        // @todo Also test always available flag?
        $draft = $contentService->updateContent(
            $contentService->createContentDraft(
                $contentService->loadContentInfo(49) // Images folder
            )->getVersionInfo(),
            $objUpdateStruct
        );

        $contentService->publishVersion(
            $draft->getVersionInfo()
        );

        $this->refreshSearch($repository);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LanguageCode
     */
    public function testSearchOnNotExistingLanguageGivesException()
    {
        $this->expectException(NotFoundException::class);

        $setupFactory = $this->getSetupFactory();
        if ($setupFactory instanceof LegacySetupFactory) {
            $this->markTestSkipped('Skipped on Solr as it is not clear that SPI search should have to validate Criterion values, in this case language code');
        }

        $query = new Query();
        $query->filter = new LanguageCode(['nor-NO']);
        $this->getRepository()->getSearchService()->findContent($query);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LanguageCode
     */
    public function testSearchOnUsedLanguageGivesOneResult()
    {
        $query = new Query();
        $query->filter = new LanguageCode(['por-PT'], false);
        $results = $this->getRepository()->getSearchService()->findContent($query);

        $this->assertEquals(1, $results->totalCount);
        $this->assertCount(1, $results->searchHits);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LanguageCode
     */
    public function testSearchOnStandardLanguageGivesManyResult()
    {
        $query = new Query();
        $query->filter = new LanguageCode(['eng-US'], false);
        $query->limit = 50;
        $results = $this->getRepository()->getSearchService()->findContent($query);

        $this->assertEquals(16, $results->totalCount);
        $this->assertEquals($results->totalCount, count($results->searchHits));
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LanguageCode
     */
    public function testSearchOnNotUsedInstalledLanguageGivesNoResult()
    {
        $query = new Query();
        $query->filter = new LanguageCode(['eng-GB'], false);
        $results = $this->getRepository()->getSearchService()->findContent($query);

        $this->assertEquals(2, $results->totalCount);
        $this->assertEquals($results->totalCount, count($results->searchHits));
    }
}

class_alias(EZP20018LanguageTest::class, 'eZ\Publish\API\Repository\Tests\Regression\EZP20018LanguageTest');
