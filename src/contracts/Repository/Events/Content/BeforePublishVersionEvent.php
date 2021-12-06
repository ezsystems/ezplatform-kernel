<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use UnexpectedValueException;

final class BeforePublishVersionEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content|null */
    private $content;

    /** @var string[] */
    private $translations;

    public function __construct(VersionInfo $versionInfo, array $translations)
    {
        $this->versionInfo = $versionInfo;
        $this->translations = $translations;
    }

    public function getVersionInfo(): VersionInfo
    {
        return $this->versionInfo;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getContent(): Content
    {
        if (!$this->hasContent()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasContent() or set it using setContent() before you call the getter.', Content::class));
        }

        return $this->content;
    }

    public function setContent(?Content $content): void
    {
        $this->content = $content;
    }

    public function hasContent(): bool
    {
        return $this->content instanceof Content;
    }
}

class_alias(BeforePublishVersionEvent::class, 'eZ\Publish\API\Repository\Events\Content\BeforePublishVersionEvent');
