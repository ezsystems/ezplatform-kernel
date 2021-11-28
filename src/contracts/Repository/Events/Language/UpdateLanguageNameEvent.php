<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Language;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;

final class UpdateLanguageNameEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $updatedLanguage;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var string */
    private $newName;

    public function __construct(
        Language $updatedLanguage,
        Language $language,
        string $newName
    ) {
        $this->updatedLanguage = $updatedLanguage;
        $this->language = $language;
        $this->newName = $newName;
    }

    public function getUpdatedLanguage(): Language
    {
        return $this->updatedLanguage;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getNewName(): string
    {
        return $this->newName;
    }
}

class_alias(UpdateLanguageNameEvent::class, 'eZ\Publish\API\Repository\Events\Language\UpdateLanguageNameEvent');
