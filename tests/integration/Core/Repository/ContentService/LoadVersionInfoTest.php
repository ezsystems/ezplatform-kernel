<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\ContentService;

use Ibexa\Tests\Integration\Core\RepositoryTestCase;

/**
 * @covers \eZ\Publish\API\Repository\ContentService
 */
final class LoadVersionInfoTest extends RepositoryTestCase
{
    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function testLoadVersionInfoListByContentInfo(): void
    {
        $contentService = self::getContentService();

        $folder1 = $this->createFolder(['eng-GB' => 'Folder1'], 2);
        $folder2 = $this->createFolder(['eng-GB' => 'Folder2'], 2);

        $versionInfoList = $contentService->loadVersionInfoListByContentInfo(
            [
                $folder1->getVersionInfo()->getContentInfo(),
                $folder2->getVersionInfo()->getContentInfo(),
            ]
        );

        self::assertCount(2, $versionInfoList);

        foreach ($versionInfoList as $versionInfo) {
            $loadedVersionInfo = $contentService->loadVersionInfo(
                $versionInfo->getContentInfo(),
                $versionInfo->versionNo
            );
            self::assertEquals($loadedVersionInfo, $versionInfo);
        }
    }

    public function testLoadVersionInfoListByContentInfoForTopLevelNode(): void
    {
        $contentService = self::getContentService();
        $locationService = self::getLocationService();

        $location = $locationService->loadLocation(1);

        $versionInfoList = $contentService->loadVersionInfoListByContentInfo(
            [$location->getContentInfo()]
        );

        self::assertCount(0, $versionInfoList);
    }
}
