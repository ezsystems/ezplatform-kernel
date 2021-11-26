<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Filtering;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

class TestContentProvider
{
    public const ENG_GB = 'eng-GB';
    public const ENG_US = 'eng-US';
    public const CONTENT_REMOTE_IDS = [
        'parent' => 'content-remote-id-parent-folder',
        'folder1' => 'content-remote-id-folder1',
        'folder2' => 'content-remote-id-folder2',
        'no-location' => 'content-remote-id-folder-without-location',
        'article1' => 'remote-id-article-1',
        'article2' => 'remote-id-article-2',
        'article3' => 'remote-id-article-3',
    ];

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Tests\Integration\Core\Repository\BaseTest */
    private $testInstance;

    public function __construct(Repository $repository, BaseTest $testInstance)
    {
        $this->repository = $repository;
        $this->testInstance = $testInstance;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createSharedContentStructure(): Content
    {
        $locationService = $this->repository->getLocationService();
        $contentService = $this->repository->getContentService();

        try {
            // see if the data is already there
            return $contentService->loadContentByRemoteId(self::CONTENT_REMOTE_IDS['parent']);
        } catch (NotFoundException $e) {
            // don't do anything
        }

        $parentFolder = $this->testInstance->createFolder(
            [
                self::ENG_GB => 'English Folder',
                self::ENG_US => 'American Folder',
            ],
            2,
            self::CONTENT_REMOTE_IDS['parent'],
        );
        $rootLocationId = $parentFolder->contentInfo->mainLocationId;
        $this->testInstance->createFolder(
            [
                self::ENG_GB => 'Nested English Folder 1',
                self::ENG_US => 'Nested American Folder 1',
            ],
            $rootLocationId,
            self::CONTENT_REMOTE_IDS['folder1'],
        );
        $folder2 = $this->testInstance->createFolder(
            [
                self::ENG_GB => 'Nested English Folder 2',
                self::ENG_US => 'Nested American Folder 2',
            ],
            $rootLocationId,
            self::CONTENT_REMOTE_IDS['folder2']
        );
        // create extra Location for 2nd folder
        $locationService->createLocation(
            $folder2->contentInfo,
            $locationService->newLocationCreateStruct(2)
        );

        $this->testInstance->createFolder(
            [
                self::ENG_US => 'Folder w/o Location',
            ],
            null,
            self::CONTENT_REMOTE_IDS['no-location']
        );

        $this->createArticle('Article 1', $rootLocationId, self::CONTENT_REMOTE_IDS['article1']);
        $this->createArticle('Article 2', $rootLocationId, self::CONTENT_REMOTE_IDS['article2']);
        $this->createArticle(
            'Article 3',
            $rootLocationId,
            self::CONTENT_REMOTE_IDS['article3'],
            'new_section'
        );

        return $parentFolder;
    }

    /**
     * @param string $contentTypeIdentifier
     * @param array $multilingualFields structure:
     * <code>
     * [
     *      '&lt;field_definition_identifier&gt;' =>
     *      [
     *          '&lt;language_code&gt;' => &lt;value&gt;
     *      ]
     * ]
     * </code>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createContentDraft(
        string $contentTypeIdentifier,
        array $multilingualFields,
        ?int $parentLocationId = null
    ): Content {
        $contentTypeService = $this->repository->getContentTypeService();
        $contentService = $this->repository->getContentService();

        $locationCreateStructList = [];
        if (null !== $parentLocationId) {
            $locationCreateStructList = [
                $this->repository->getLocationService()->newLocationCreateStruct($parentLocationId),
            ];
        }

        $folderType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        // first language of first Field is to be main one
        $mainLanguageCode = array_keys(array_values($multilingualFields)[0])[0];
        $contentCreate = $contentService->newContentCreateStruct($folderType, $mainLanguageCode);
        foreach ($multilingualFields as $fieldDefinitionIdentifier => $translations) {
            foreach ($translations as $languageCode => $value) {
                $contentCreate->setField($fieldDefinitionIdentifier, $value, $languageCode);
            }
        }

        return $contentService->createContent($contentCreate, $locationCreateStructList);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function createArticle(
        string $title,
        int $parentLocationId,
        string $remoteId,
        ?string $sectionName = null
    ): Content {
        $contentTypeService = $this->repository->getContentTypeService();
        $contentService = $this->repository->getContentService();
        $locationService = $this->repository->getLocationService();

        $articleType = $contentTypeService->loadContentTypeByIdentifier('article');
        $articleCreate = $contentService->newContentCreateStruct($articleType, self::ENG_GB);
        $articleCreate->remoteId = $remoteId;
        if (null !== $sectionName) {
            $articleCreate->sectionId = $this->createSection($sectionName)->id;
        }
        $articleCreate->setField('title', $title);
        $contentDraft = $contentService->createContent(
            $articleCreate,
            [$locationService->newLocationCreateStruct($parentLocationId)]
        );

        return $contentService->publishVersion($contentDraft->getVersionInfo());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function createSection(string $sectionIdentifier): Section
    {
        $sectionService = $this->repository->getSectionService();
        $sectionCreate = $sectionService->newSectionCreateStruct();
        $sectionCreate->identifier = $sectionIdentifier;
        $sectionCreate->name = ucfirst($sectionIdentifier);

        return $sectionService->createSection($sectionCreate);
    }
}

class_alias(TestContentProvider::class, 'eZ\Publish\API\Repository\Tests\Filtering\TestContentProvider');
