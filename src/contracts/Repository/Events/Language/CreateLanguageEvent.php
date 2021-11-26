<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Language;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct;

final class CreateLanguageEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct */
    private $languageCreateStruct;

    public function __construct(
        Language $language,
        LanguageCreateStruct $languageCreateStruct
    ) {
        $this->language = $language;
        $this->languageCreateStruct = $languageCreateStruct;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getLanguageCreateStruct(): LanguageCreateStruct
    {
        return $this->languageCreateStruct;
    }
}

class_alias(CreateLanguageEvent::class, 'eZ\Publish\API\Repository\Events\Language\CreateLanguageEvent');
