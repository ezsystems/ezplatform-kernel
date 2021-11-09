<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\LanguageService as APIService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct;
use Ibexa\Core\Repository\SiteAccessAware\LanguageService;

class LanguageServiceTest extends AbstractServiceTest
{
    public function getAPIServiceClassName()
    {
        return APIService::class;
    }

    public function getSiteAccessAwareServiceClassName()
    {
        return LanguageService::class;
    }

    public function providerForPassTroughMethods()
    {
        $languageCreateStruct = new LanguageCreateStruct();
        $language = new Language();

        // string $method, array $arguments, bool $return = true
        return [
            ['createLanguage', [$languageCreateStruct], $language],

            ['updateLanguageName', [$language, 'Afrikaans'], $language],

            ['enableLanguage', [$language], $language],

            ['disableLanguage', [$language], $language],

            ['loadLanguage', ['eng-GB'], $language],
            ['loadLanguageListByCode', [['eng-GB']], []],

            ['loadLanguages', [], []],

            ['loadLanguageById', [4], $language],
            ['loadLanguageListById', [[4]], []],

            ['deleteLanguage', [$language], null],

            ['getDefaultLanguageCode', [], ''],

            ['newLanguageCreateStruct', [], $languageCreateStruct],
        ];
    }

    public function providerForLanguagesLookupMethods()
    {
        // string $method, array $arguments, bool $return, int $languageArgumentIndex
        return [];
    }
}

class_alias(LanguageServiceTest::class, 'eZ\Publish\Core\Repository\SiteAccessAware\Tests\LanguageServiceTest');
