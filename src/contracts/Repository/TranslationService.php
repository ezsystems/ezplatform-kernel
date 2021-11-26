<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Repository;

use Ibexa\Contracts\Core\Repository\Values\Translation;

/**
 * Interface for a translation service.
 *
 * Implement this to use translation backends like Symfony2 Translate, gettext
 * or ezcTranslation.
 *
 * Call the translation method with the current target locale from your
 * templates, for example.
 */
interface TranslationService
{
    /**
     * Translate.
     *
     * Translate a Translation value object.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Translation $translation
     * @param string $locale
     *
     * @return string
     */
    public function translate(Translation $translation, $locale);

    /**
     * Translate string.
     *
     * Translate a string. Strings could be useful for the simplest cases.
     * Usually you will always use Translation value objects for this.
     *
     * @param string $translation
     * @param string $locale
     *
     * @return string
     */
    public function translateString($translation, $locale);
}

class_alias(TranslationService::class, 'eZ\Publish\API\Repository\TranslationService');
