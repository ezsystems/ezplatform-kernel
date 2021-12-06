<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class CreateContentDraftEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $contentDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $creator;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null */
    private $language;

    public function __construct(
        Content $contentDraft,
        ContentInfo $contentInfo,
        ?VersionInfo $versionInfo = null,
        ?User $creator = null,
        ?Language $language = null
    ) {
        $this->contentDraft = $contentDraft;
        $this->contentInfo = $contentInfo;
        $this->versionInfo = $versionInfo;
        $this->creator = $creator;
        $this->language = $language;
    }

    public function getContentDraft(): Content
    {
        return $this->contentDraft;
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
}

class_alias(CreateContentDraftEvent::class, 'eZ\Publish\API\Repository\Events\Content\CreateContentDraftEvent');
