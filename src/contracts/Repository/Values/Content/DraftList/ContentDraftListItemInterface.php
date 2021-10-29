<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\DraftList;

use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

interface ContentDraftListItemInterface
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo|null
     */
    public function getVersionInfo(): ?VersionInfo;

    /**
     * @return bool
     */
    public function hasVersionInfo(): bool;
}

class_alias(ContentDraftListItemInterface::class, 'eZ\Publish\API\Repository\Values\Content\DraftList\ContentDraftListItemInterface');
