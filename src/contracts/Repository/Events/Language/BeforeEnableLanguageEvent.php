<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Language;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use UnexpectedValueException;

final class BeforeEnableLanguageEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null */
    private $enabledLanguage;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getEnabledLanguage(): Language
    {
        if (!$this->hasEnabledLanguage()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasEnabledLanguage() or set it using setEnabledLanguage() before you call the getter.', Language::class));
        }

        return $this->enabledLanguage;
    }

    public function setEnabledLanguage(?Language $enabledLanguage): void
    {
        $this->enabledLanguage = $enabledLanguage;
    }

    public function hasEnabledLanguage(): bool
    {
        return $this->enabledLanguage instanceof Language;
    }
}

class_alias(BeforeEnableLanguageEvent::class, 'eZ\Publish\API\Repository\Events\Language\BeforeEnableLanguageEvent');
