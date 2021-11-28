<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\Language;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator;
use Ibexa\Tests\Core\Persistence\Legacy\Content\LanguageAwareTestCase;

class MaskGeneratorTest extends LanguageAwareTestCase
{
    /**
     * @param array $languages
     * @param int $expectedMask
     *
     * @dataProvider getLanguageMaskData
     */
    public function testGenerateLanguageMask(array $languages, $expectedMask)
    {
        $generator = $this->getMaskGenerator();

        $this->assertSame(
            $expectedMask,
            $generator->generateLanguageMask($languages)
        );
    }

    /**
     * @param array $languages
     * @param int $expectedMask
     *
     *
     * @dataProvider getLanguageMaskData
     */
    public function testGenerateLanguageMaskFromLanguagesCodes(array $languages, $expectedMask)
    {
        $generator = $this->getMaskGenerator();

        if (isset($languages['always-available'])) {
            $isAlwaysAvailable = true;
            unset($languages['always-available']);
        } else {
            $isAlwaysAvailable = false;
        }

        $this->assertSame(
            $expectedMask,
            $generator->generateLanguageMaskFromLanguageCodes(array_keys($languages), $isAlwaysAvailable)
        );
    }

    /**
     * Returns test data for {@link testGenerateLanguageMask()} and {@link testGenerateLanguageMaskFromLanguagesCodes()}.
     *
     * @return array
     */
    public static function getLanguageMaskData()
    {
        return [
            'error' => [
                [],
                0,
            ],
            'single_lang' => [
                ['eng-GB' => true],
                4,
            ],
            'multi_lang' => [
                ['eng-US' => true, 'eng-GB' => true],
                6,
            ],
            'always_available' => [
                ['always-available' => 'eng-US', 'eng-US' => true],
                3,
            ],
            'full' => [
                ['always-available' => 'eng-US', 'eng-US' => true, 'eng-GB' => true],
                7,
            ],
        ];
    }

    /**
     * @param string $languageCode
     * @param bool $alwaysAvailable
     * @param int $expectedIndicator
     *
     * @dataProvider getLanguageIndicatorData
     */
    public function testGenerateLanguageIndicator(
        $languageCode,
        $alwaysAvailable,
        $expectedIndicator
    ) {
        $generator = $this->getMaskGenerator();

        $this->assertSame(
            $expectedIndicator,
            $generator->generateLanguageIndicator($languageCode, $alwaysAvailable)
        );
    }

    /**
     * Returns test data for {@link testGenerateLanguageIndicator()}.
     *
     * @return array
     */
    public static function getLanguageIndicatorData()
    {
        return [
            'not_available' => [
                'eng-GB',
                false,
                4,
            ],
            'always_available' => [
                'eng-US',
                true,
                3,
            ],
        ];
    }

    public function testIsLanguageAlwaysAvailable()
    {
        $generator = $this->getMaskGenerator();

        $this->assertTrue(
            $generator->isLanguageAlwaysAvailable(
                'eng-GB',
                [
                    'always-available' => 'eng-GB',
                    'eng-GB' => 'lala',
                ]
            )
        );
    }

    public function testIsLanguageAlwaysAvailableOtherLanguage()
    {
        $generator = $this->getMaskGenerator();

        $this->assertFalse(
            $generator->isLanguageAlwaysAvailable(
                'eng-GB',
                [
                    'always-available' => 'eng-US',
                    'eng-GB' => 'lala',
                ]
            )
        );
    }

    public function testIsLanguageAlwaysAvailableNoDefault()
    {
        $generator = $this->getMaskGenerator();

        $this->assertFalse(
            $generator->isLanguageAlwaysAvailable(
                'eng-GB',
                [
                    'eng-GB' => 'lala',
                ]
            )
        );
    }

    /**
     * @param int $langMask
     * @param bool $expectedResult
     *
     * @dataProvider isAlwaysAvailableProvider
     */
    public function testIsAlwaysAvailable($langMask, $expectedResult)
    {
        $generator = $this->getMaskGenerator();
        self::assertSame($expectedResult, $generator->isAlwaysAvailable($langMask));
    }

    /**
     * Returns test data for {@link testIsAlwaysAvailable()}.
     *
     * @return array
     */
    public function isAlwaysAvailableProvider()
    {
        return [
            [2, false],
            [3, true],
            [62, false],
            [14, false],
            [15, true],
        ];
    }

    /**
     * @dataProvider removeAlwaysAvailableFlagProvider
     */
    public function testRemoveAlwaysAvailableFlag($langMask, $expectedResult)
    {
        $generator = $this->getMaskGenerator();
        self::assertSame($expectedResult, $generator->removeAlwaysAvailableFlag($langMask));
    }

    /**
     * Returns test data for {@link testRemoveAlwaysAvailableFlag}.
     *
     * @return array
     */
    public function removeAlwaysAvailableFlagProvider()
    {
        return [
            [3, 2],
            [7, 6],
            [14, 14],
            [62, 62],
        ];
    }

    /**
     * @param int $langMask
     * @param array $expectedResult
     *
     * @dataProvider languageIdsFromMaskProvider
     */
    public function testExtractLanguageIdsFromMask($langMask, array $expectedResult)
    {
        $generator = $this->getMaskGenerator();
        self::assertSame($expectedResult, $generator->extractLanguageIdsFromMask($langMask));
    }

    /**
     * Returns test data for {@link testExtractLanguageIdsFromMask}.
     *
     * @return array
     */
    public function languageIdsFromMaskProvider()
    {
        return [
            [
                2,
                [2],
            ],
            [
                15,
                [2, 4, 8],
            ],
            [
                62,
                [2, 4, 8, 16, 32],
            ],
        ];
    }

    /**
     * Returns the mask generator to test.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator
     */
    protected function getMaskGenerator()
    {
        return new MaskGenerator($this->getLanguageHandler());
    }

    /**
     * Returns a language handler mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Language\Handler
     */
    protected function getLanguageHandler()
    {
        if (!isset($this->languageHandler)) {
            $this->languageHandler = $this->createMock(LanguageHandler::class);
            $this->languageHandler->expects($this->any())
                                  ->method($this->anything())// loadByLanguageCode && loadListByLanguageCodes
                                  ->will(
                                      $this->returnCallback(
                                          static function ($languageCodes) {
                                              if (is_string($languageCodes)) {
                                                  $language = $languageCodes;
                                                  $languageCodes = [$language];
                                              }

                                              $languages = [];
                                              if (in_array('eng-US', $languageCodes, true)) {
                                                  $languages['eng-US'] = new Language(
                                                      [
                                                          'id' => 2,
                                                          'languageCode' => 'eng-US',
                                                          'name' => 'US english',
                                                      ]
                                                  );
                                              }

                                              if (in_array('eng-GB', $languageCodes, true)) {
                                                  $languages['eng-GB'] = new Language(
                                                      [
                                                           'id' => 4,
                                                           'languageCode' => 'eng-GB',
                                                           'name' => 'British english',
                                                       ]
                                                  );
                                              }

                                              return isset($language) ? $languages[$language] : $languages;
                                          }
                                      )
                                  );
        }

        return $this->languageHandler;
    }
}

class_alias(MaskGeneratorTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\Language\MaskGeneratorTest');
