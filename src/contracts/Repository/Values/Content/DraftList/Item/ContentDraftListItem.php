<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\DraftList\Item;

use Ibexa\Contracts\Core\Repository\Values\Content\DraftList\ContentDraftListItemInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

/**
 * Item of content drafts list.
 */
class ContentDraftListItem implements ContentDraftListItemInterface
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo
     */
    private $versionInfo;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     */
    public function __construct(VersionInfo $versionInfo)
    {
        $this->versionInfo = $versionInfo;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo|null
     */
    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    /**
     * @return bool
     */
    public function hasVersionInfo(): bool
    {
        return $this->versionInfo instanceof VersionInfo;
    }
}

class_alias(ContentDraftListItem::class, 'eZ\Publish\API\Repository\Values\Content\DraftList\Item\ContentDraftListItem');
