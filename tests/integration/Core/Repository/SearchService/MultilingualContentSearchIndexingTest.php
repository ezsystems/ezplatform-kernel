<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Tests\BaseTest;
use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class MultilingualContentSearchIndexingTest extends BaseTest
{
    private const MODIFIED_TRANSLATION = 'pol-PL';

    private const LANGUAGES = [
        'eng-US' => 'English (American)',
        'eng-GB' => 'English (United Kingdom)',
        self::MODIFIED_TRANSLATION => 'Polish (Poland)',
        'nor-NO' => 'Norwegian (Norway)',
        'ger-DE' => 'German (Germany)',
        'por-PT' => 'Portuguese (Portugal)',
    ];

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testPublishingSingleTranslationKeepsSearchIndexConsistent(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $searchService = $repository->getSearchService();
        $this->createMissingLanguages($repository->getContentLanguageService());

        // create Folder with a single translation
        $folder = $this->createFolder(['eng-US' => 'Test eng-US']);

        // add 20 translations
        $folderUpdate = $contentService->newContentUpdateStruct();
        $translations = array_keys(self::LANGUAGES);
        foreach ($translations as $translation) {
            $folderUpdate->setField('name', 'Test ' . $translation, $translation);
        }
        $folderDraft = $contentService->updateContent(
            $contentService->createContentDraft($folder->contentInfo)->getVersionInfo(),
            $folderUpdate
        );
        $folder = $contentService->publishVersion($folderDraft->getVersionInfo());

        // update single translation
        $folderUpdate = $contentService->newContentUpdateStruct();
        $folderUpdate->setField('name', 'Updated Polish version', self::MODIFIED_TRANSLATION);
        $folderDraft = $contentService->updateContent(
            $contentService->createContentDraft($folder->contentInfo)->getVersionInfo(),
            $folderUpdate
        );
        $folder = $contentService->publishVersion(
            $folderDraft->getVersionInfo(),
            [self::MODIFIED_TRANSLATION]
        );

        $this->refreshSearch($repository);

        $query = new Query([
            'filter' => new Criterion\ContentId(
                [$folder->id]
            ),
        ]);
        $searchResult = $searchService->findContent($query, ['languages' => $translations]);
        self::assertSame(1, $searchResult->totalCount);
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $foundContent */
        $foundContent = $searchResult->searchHits[0]->valueObject;
        $expectedContentInfo = $foundContent->contentInfo;
        $expectedVersionNo = $foundContent->getVersionInfo()->versionNo;

        $searchResult = $searchService->findContent(
            $query,
            ['languages' => [self::MODIFIED_TRANSLATION]]
        );
        self::assertSame(1, $searchResult->totalCount);
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $foundContent */
        $foundContent = $searchResult->searchHits[0]->valueObject;
        self::assertEquals($expectedContentInfo, $foundContent->contentInfo);
        self::assertEquals($expectedVersionNo, $foundContent->getVersionInfo()->versionNo);
    }

    /**
     * Create required languages which are not pre-defined by Repository test setup.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function createMissingLanguages(LanguageService $languageService): void
    {
        $languages = $languageService->loadLanguages();
        $missingLanguages = array_diff(
            array_keys(self::LANGUAGES),
            array_column($languages, 'languageCode')
        );

        foreach ($missingLanguages as $languageCode) {
            $languageService->createLanguage(
                new LanguageCreateStruct(
                    [
                        'languageCode' => $languageCode,
                        'name' => self::LANGUAGES[$languageCode],
                        'enabled' => true,
                    ]
                )
            );
        }
    }
}
