<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use InvalidArgumentException;

abstract class RepositoryTestCase extends IbexaKernelTestCase
{
    public const CONTENT_TREE_ROOT_ID = 2;

    private const CONTENT_TYPE_FOLDER_IDENTIFIER = 'folder';

    protected function setUp(): void
    {
        parent::setUp();

        self::loadSchema();
        self::loadFixtures();

        self::setAdministratorUser();
    }

    /**
     * @param array<string, string> $names
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function createFolder(array $names, int $parentLocationId = self::CONTENT_TREE_ROOT_ID): Content
    {
        $contentService = self::getContentService();
        $draft = $this->createFolderDraft($names, $parentLocationId);

        return $contentService->publishVersion($draft->getVersionInfo());
    }

    /**
     * @param array<string, string> $names
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function createFolderDraft(array $names, int $parentLocationId = self::CONTENT_TREE_ROOT_ID): Content
    {
        if (empty($names)) {
            throw new InvalidArgumentException(__METHOD__ . ' requires $names to be not empty');
        }

        $contentService = self::getContentService();
        $contentTypeService = self::getContentTypeService();
        $locationService = self::getLocationService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_FOLDER_IDENTIFIER);
        $mainLanguageCode = array_keys($names)[0];
        $contentCreateStruct = $contentService->newContentCreateStruct($folderType, $mainLanguageCode);
        foreach ($names as $languageCode => $name) {
            $contentCreateStruct->setField('name', $name, $languageCode);
        }

        return $contentService->createContent(
            $contentCreateStruct,
            [
                $locationService->newLocationCreateStruct($parentLocationId),
            ]
        );
    }
}
