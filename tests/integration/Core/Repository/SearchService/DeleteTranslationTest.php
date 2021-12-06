<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LanguageLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

/**
 * Test case for delete content translation with the SearchService.
 *
 * @covers \Ibexa\Contracts\Core\Repository\SearchService
 * @group integration
 * @group search
 */
final class DeleteTranslationTest extends BaseTest
{
    /**
     * @param array $languages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function createTestContentWithLanguages(array $languages): Content
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();

        $contentTypeArticle = $contentTypeService->loadContentTypeByIdentifier('article');
        $contentCreateStructArticle = $contentService->newContentCreateStruct(
            $contentTypeArticle,
            'eng-GB'
        );

        foreach ($languages as $langCode => $title) {
            $contentCreateStructArticle->setField('title', $title, $langCode);
        }

        $locationCreateStructArticle = $locationService->newLocationCreateStruct(2);
        $draftArticle = $contentService->createContent(
            $contentCreateStructArticle,
            [$locationCreateStructArticle]
        );
        $content = $contentService->publishVersion($draftArticle->getVersionInfo());
        $this->refreshSearch($repository);

        return $content;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function findContent(string $text, string $languageCode): SearchResult
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $query = new Query();
        $query->query = new Criterion\FullText($text);
        $query->limit = 0;
        $languageFilter = [
            'languages' => [$languageCode],
            'useAlwaysAvailable' => true,
            'excludeTranslationsFromAlwaysAvailable' => false,
        ];

        return $searchService->findContent($query, $languageFilter);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteContentTranslation(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $testContent = $this->createFolder(['eng-GB' => 'Contact', 'ger-DE' => 'Kontakt'], 2);
        $this->createFolder(['eng-GB' => 'OtherEngContent', 'ger-DE' => 'OtherGerContent'], 2);
        $this->refreshSearch($repository);

        $searchResult = $this->findContent('Kontakt', 'ger-DE');
        $this->assertEquals(1, $searchResult->totalCount);

        $contentService->deleteTranslation($testContent->contentInfo, 'ger-DE');
        $this->refreshSearch($repository);
        $searchResult = $this->findContent('Kontakt', 'ger-DE');
        $this->assertEquals(
            0,
            $searchResult->totalCount,
            'Found reference to the deleted Content translation'
        );

        // check if unrelated items were not affected
        $searchResult = $this->findContent('OtherGerContent', 'ger-DE');
        $this->assertEquals(1, $searchResult->totalCount, 'Unrelated translation was deleted');
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteContentTranslationWithContentRemovePolicy(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $testContent = $this->createFolder(['eng-GB' => 'Contact', 'ger-DE' => 'Kontakt'], 2);
        $this->refreshSearch($repository);

        $user = $this->provideUserWithContentRemovePolicies();
        $repository->getPermissionResolver()->setCurrentUserReference($user);

        $searchResult = $this->findContent('Kontakt', 'ger-DE');

        $this->assertEquals(1, $searchResult->totalCount);
        $contentService->deleteTranslation($testContent->contentInfo, 'ger-DE');

        $this->refreshSearch($repository);
        $searchResult = $this->findContent('Kontakt', 'ger-DE');
        $this->assertEquals(0, $searchResult->totalCount);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPreventTranslationDeletionIfNoAccess(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $testContent = $this->createFolder(
            [
                'eng-GB' => 'Contact',
                'ger-DE' => 'Kontakt',
                'eng-US' => 'Contact',
            ],
            2
        );

        $user = $this->provideUserWithContentRemovePolicies();
        $repository->getPermissionResolver()->setCurrentUserReference($user);

        $this->expectException(UnauthorizedException::class);

        $contentService->deleteTranslation($testContent->contentInfo, 'eng-US');
    }

    public function provideUserWithContentRemovePolicies(): User
    {
        $limitations = [
            new LanguageLimitation(['limitationValues' => ['ger-DE']]),
        ];

        return $this->createUserWithPolicies(
            'test',
            [
                ['module' => 'content', 'function' => 'remove', 'limitations' => $limitations],
                ['module' => 'content', 'function' => 'versionread'],
                ['module' => 'content', 'function' => 'read'],
            ]
        );
    }
}

class_alias(DeleteTranslationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\DeleteTranslationTest');
