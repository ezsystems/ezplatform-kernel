<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Helper;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

/**
 * Loads a location based on a ContentInfo.
 */
interface ContentInfoLocationLoader
{
    /**
     * Loads a location from a ContentInfo.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the location doesn't have a contentId.
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the location failed to load.
     */
    public function loadLocation(ContentInfo $contentInfo);
}

class_alias(ContentInfoLocationLoader::class, 'eZ\Publish\Core\Helper\ContentInfoLocationLoader');
