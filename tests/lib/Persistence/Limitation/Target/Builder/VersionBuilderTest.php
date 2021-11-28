<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Limitation\Target\Builder;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Limitation\Target\Builder\VersionBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\Core\Limitation\Target\Builder\VersionBuilder
 */
class VersionBuilderTest extends TestCase
{
    /** @var string */
    private const GER_DE = 'ger-DE';

    /** @var string */
    private const ENG_US = 'eng-US';

    /** @var string */
    private const ENG_GB = 'eng-GB';

    /**
     * Data provider for testBuild.
     *
     * @see testBuild
     *
     * @return array
     */
    public function providerForTestBuild(): array
    {
        $versionStatuses = [
            VersionInfo::STATUS_DRAFT,
            VersionInfo::STATUS_PUBLISHED,
            VersionInfo::STATUS_ARCHIVED,
        ];

        $data = [];
        foreach ($versionStatuses as $versionStatus) {
            $languagesList = [self::GER_DE, self::ENG_US, self::ENG_GB];
            $contentTypeIdsList = [1, 2];
            $initialLanguageCode = self::ENG_US;
            $fields = [
                new Field(['languageCode' => self::GER_DE]),
                new Field(['languageCode' => self::GER_DE]),
                new Field(['languageCode' => self::ENG_US]),
            ];
            $updateTranslationsLanguageCodes = [self::GER_DE, self::ENG_US];
            $publishLanguageCodes = [self::GER_DE, self::ENG_US];

            $data[] = [
                new Target\Version(
                    [
                        'newStatus' => $versionStatus,
                        'allLanguageCodesList' => $languagesList,
                        'allContentTypeIdsList' => $contentTypeIdsList,
                        'forUpdateLanguageCodesList' => $updateTranslationsLanguageCodes,
                        'forUpdateInitialLanguageCode' => $initialLanguageCode,
                        'forPublishLanguageCodesList' => $publishLanguageCodes,
                    ]
                ),
                $versionStatus,
                $initialLanguageCode,
                $fields,
                $languagesList,
                $contentTypeIdsList,
                $publishLanguageCodes,
            ];

            // no published content
            $data[] = [
                new Target\Version(
                    [
                        'newStatus' => $versionStatus,
                        'allLanguageCodesList' => $languagesList,
                        'allContentTypeIdsList' => $contentTypeIdsList,
                        'forUpdateLanguageCodesList' => $updateTranslationsLanguageCodes,
                        'forUpdateInitialLanguageCode' => $initialLanguageCode,
                        'forPublishLanguageCodesList' => $publishLanguageCodes,
                    ]
                ),
                $versionStatus,
                $initialLanguageCode,
                $fields,
                $languagesList,
                $contentTypeIdsList,
                $publishLanguageCodes,
            ];
        }

        return $data;
    }

    /**
     * @dataProvider providerForTestBuild
     *
     * @param \Ibexa\Contracts\Core\Limitation\Target\Version $expectedTargetVersion
     * @param int $newStatus
     * @param string $initialLanguageCode
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field[] $newFields
     * @param string[] $languagesList
     * @param int[] $contentTypeIdsList
     * @param string[] $publishLanguageCodes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testBuild(
        Target\Version $expectedTargetVersion,
        int $newStatus,
        string $initialLanguageCode,
        array $newFields,
        array $languagesList,
        array $contentTypeIdsList,
        array $publishLanguageCodes
    ): void {
        $versionBuilder = new VersionBuilder();
        $versionBuilder
            ->changeStatusTo($newStatus)
            ->updateFieldsTo($initialLanguageCode, $newFields)
            ->translateToAnyLanguageOf($languagesList)
            ->createFromAnyContentTypeOf($contentTypeIdsList)
            ->publishTranslations($publishLanguageCodes)
        ;

        self::assertInstanceOf(VersionBuilder::class, $versionBuilder);
        self::assertEquals($expectedTargetVersion, $versionBuilder->build());
    }
}

class_alias(VersionBuilderTest::class, 'eZ\Publish\SPI\Tests\Limitation\Target\Builder\VersionBuilderTest');
