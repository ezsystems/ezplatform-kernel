<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Language;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class EnableLanguageEvent extends AfterEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    private $enabledLanguage;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    private $language;

    public function __construct(
        Language $enabledLanguage,
        Language $language
    ) {
        $this->enabledLanguage = $enabledLanguage;
        $this->language = $language;
    }

    public function getEnabledLanguage(): Language
    {
        return $this->enabledLanguage;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }
}
