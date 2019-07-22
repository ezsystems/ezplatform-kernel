<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event\Language;

use eZ\Publish\API\Repository\Events\Language\EnableLanguageEvent as EnableLanguageEventInterface;
use eZ\Publish\API\Repository\Values\Content\Language;
use Symfony\Contracts\EventDispatcher\Event;

final class EnableLanguageEvent extends Event implements EnableLanguageEventInterface
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