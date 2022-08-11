<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository;

use eZ\Publish\API\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;

abstract class BaseIbexaRepositoryTestCase extends IbexaKernelTestCase
{
    protected const DEFAULT_MAIN_LANGUAGE_CODE = 'eng-GB';
    public const CONTENT_ROOT_LOCATION_ID = 2;

    protected function setUp(): void
    {
        self::bootKernel();

        self::loadSchema();
        self::loadFixtures();

        self::setAdministratorUser();
    }

    /**
     * @param array<string, string> $names language code to name map
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    protected function createMultilingualFolder(array $names, int $parentLocationId): Content
    {
        self::assertNotEmpty($names, 'createMultilingualFolder: no folder names given');

        $contentService = self::getContentService();
        $locationService = self::getLocationService();
        $contentTypeService = self::getContentTypeService();
        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');

        $contentCreate = $contentService->newContentCreateStruct(
            $folderType,
            key($names)
        );
        foreach ($names as $languageCode => $name) {
            $contentCreate->setField('name', $name, $languageCode);
        }

        $folderDraft = $contentService->createContent(
            $contentCreate,
            [$locationService->newLocationCreateStruct($parentLocationId)]
        );

        return $contentService->publishVersion($folderDraft->getVersionInfo());
    }

    /**
     * @param array<string, string> $newNames language code to name map
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    protected function updateFolderName(
        Content $folder,
        array $newNames
    ): Content {
        $contentService = self::getContentService();
        $folderUpdate = $contentService->newContentUpdateStruct();
        foreach ($newNames as $languageCode => $newName) {
            $folderUpdate->setField('name', $newName, $languageCode);
        }

        $draft = $contentService->createContentDraft($folder->getVersionInfo()->getContentInfo());

        return $contentService->publishVersion(
            $contentService->updateContent(
                $draft->getVersionInfo(),
                $folderUpdate
            )->getVersionInfo()
        );
    }
}
