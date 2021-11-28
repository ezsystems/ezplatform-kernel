<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Language;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;

final class DisableLanguageEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $disabledLanguage;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    public function __construct(
        Language $disabledLanguage,
        Language $language
    ) {
        $this->disabledLanguage = $disabledLanguage;
        $this->language = $language;
    }

    public function getDisabledLanguage(): Language
    {
        return $this->disabledLanguage;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }
}

class_alias(DisableLanguageEvent::class, 'eZ\Publish\API\Repository\Events\Language\DisableLanguageEvent');
