<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\Values\Content;

use eZ\Publish\API\Repository\Tests\BaseTest;

final class DefaultLanguageCodeForContentTest extends BaseTest
{
    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testDefaultLanguageCodeForCreatedContentWithoutPrioritizedLanguage(): void
    {
        $names = [
            'eng-GB' => 'Test GB',
            'ger-DE' => 'Test DE',
            'eng-US' => 'Test US',
        ];
        $testFolder = $this->createFolder(
            $names,
            2,
            null,
            false
        );

        self::assertEquals('eng-GB', $testFolder->getDefaultLanguageCode());
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testDefaultLanguageCodeForCreatedContentWithPrioritizedLanguage(): void
    {
        $names = [
            'eng-GB' => 'Test GB',
            'ger-DE' => 'Test DE',
            'eng-US' => 'Test US',
        ];

        $testFolder = $this->createFolder(
            $names,
            2,
            null,
            false
        );

        $repository = $this->getRepository();
        $testFolderInGerman = $repository->getContentService()->loadContent($testFolder->id, ['ger-DE']);

        self::assertEquals('ger-DE', $testFolderInGerman->getDefaultLanguageCode());
    }
}
