<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use UnexpectedValueException;

final class BeforeCreateContentDraftEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $creator;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null */
    private $language;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content|null */
    private $contentDraft;

    public function __construct(
        ContentInfo $contentInfo,
        ?VersionInfo $versionInfo = null,
        ?User $creator = null,
        ?Language $language = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->versionInfo = $versionInfo;
        $this->creator = $creator;
        $this->language = $language;
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }

    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function getContentDraft(): Content
    {
        if (!$this->hasContentDraft()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasContentDraft() or set it using setContentDraft() before you call the getter.', Content::class));
        }

        return $this->contentDraft;
    }

    public function setContentDraft(?Content $contentDraft): void
    {
        $this->contentDraft = $contentDraft;
    }

    public function hasContentDraft(): bool
    {
        return $this->contentDraft instanceof Content;
    }
}

class_alias(BeforeCreateContentDraftEvent::class, 'eZ\Publish\API\Repository\Events\Content\BeforeCreateContentDraftEvent');
