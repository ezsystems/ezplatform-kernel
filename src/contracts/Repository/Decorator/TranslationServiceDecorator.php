<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\TranslationService;
use Ibexa\Contracts\Core\Repository\Values\Translation;

abstract class TranslationServiceDecorator implements TranslationService
{
    /** @var \Ibexa\Contracts\Core\Repository\TranslationService */
    protected $innerService;

    public function __construct(TranslationService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function translate(
        Translation $translation,
        $locale
    ) {
        return $this->innerService->translate($translation, $locale);
    }

    public function translateString(
        $translation,
        $locale
    ) {
        return $this->innerService->translateString($translation, $locale);
    }
}

class_alias(TranslationServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\TranslationServiceDecorator');
