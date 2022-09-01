<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\URLAliasService;

use Ibexa\Tests\Integration\Core\Repository\BaseIbexaRepositoryTestCase;

final class MultilingualURLAliasArchivingTest extends BaseIbexaRepositoryTestCase
{
    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->urlAliasService = self::getUrlAliasService();
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function testLookupOnRenamedPathForSingleLanguage(): void
    {
        $folder = $this->createMultilingualFolder(
            [
                'eng-GB' => 'test',
                'ger-DE' => 'test',
            ],
            self::CONTENT_ROOT_LOCATION_ID
        );
        $mainLocationId = $folder->getVersionInfo()->getContentInfo()->getMainLocation()->id;

        // sanity check
        //$this->assertLookupSystemAlias('/test', null, ['ger-DE', 'eng-GB'], false, $mainLocationId);

        $this->updateFolderName($folder, ['eng-GB' => 'test-2']);

        $this->assertLookupSystemAlias('/test', 'ger-DE', ['ger-DE'], false, $mainLocationId);
        $this->assertLookupSystemAlias('/test', 'eng-GB', ['eng-GB'], true, $mainLocationId);
    }

    /**
     * @param string[] $expectedUrlAliasLanguageCodes
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function assertLookupSystemAlias(
        string $path,
        ?string $languageCode,
        array $expectedUrlAliasLanguageCodes,
        bool $expectedIsHistory,
        int $expectedDestination
    ): void {
        $messagePrefix = sprintf(
            'lookup for %s with language code: %s',
            $path,
            $languageCode ?? 'none'
        );
        $urlAlias = $this->urlAliasService->lookup($path, $languageCode);

        self::assertSame($expectedUrlAliasLanguageCodes, $urlAlias->languageCodes);
        self::assertSame(
            $expectedIsHistory,
            $urlAlias->isHistory,
            $messagePrefix . sprintf(
                'expected %s be archived',
                $expectedIsHistory ? 'to' : 'not to'
            )
        );
        // system aliases are never custom, nor forward (archived entries self-include forwarding)
        self::assertFalse($urlAlias->isCustom, $messagePrefix . 'can\'t be custom');
        self::assertFalse($urlAlias->forward, $messagePrefix . 'can\'t forward');
        self::assertEquals(
            $expectedDestination,
            $urlAlias->destination,
            $messagePrefix . 'expected destination differs'
        );
    }
}
