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

final class BeforeUpdateLanguageNameEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var string */
    private $newName;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null */
    private $updatedLanguage;

    public function __construct(Language $language, string $newName)
    {
        $this->language = $language;
        $this->newName = $newName;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getNewName(): string
    {
        return $this->newName;
    }

    public function getUpdatedLanguage(): Language
    {
        if (!$this->hasUpdatedLanguage()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedLanguage() or set it using setUpdatedLanguage() before you call the getter.', Language::class));
        }

        return $this->updatedLanguage;
    }

    public function setUpdatedLanguage(?Language $updatedLanguage): void
    {
        $this->updatedLanguage = $updatedLanguage;
    }

    public function hasUpdatedLanguage(): bool
    {
        return $this->updatedLanguage instanceof Language;
    }
}

class_alias(BeforeUpdateLanguageNameEvent::class, 'eZ\Publish\API\Repository\Events\Language\BeforeUpdateLanguageNameEvent');
