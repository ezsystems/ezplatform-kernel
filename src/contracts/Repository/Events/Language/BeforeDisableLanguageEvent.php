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

final class BeforeDisableLanguageEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null */
    private $disabledLanguage;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getDisabledLanguage(): Language
    {
        if (!$this->hasDisabledLanguage()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasDisabledLanguage() or set it using setDisabledLanguage() before you call the getter.', Language::class));
        }

        return $this->disabledLanguage;
    }

    public function setDisabledLanguage(?Language $disabledLanguage): void
    {
        $this->disabledLanguage = $disabledLanguage;
    }

    public function hasDisabledLanguage(): bool
    {
        return $this->disabledLanguage instanceof Language;
    }
}

class_alias(BeforeDisableLanguageEvent::class, 'eZ\Publish\API\Repository\Events\Language\BeforeDisableLanguageEvent');
