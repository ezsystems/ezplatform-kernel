<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail;

use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

interface ThumbnailStrategy
{
    public function getThumbnail(ContentType $contentType, array $fields, ?VersionInfo $versionInfo = null): ?Thumbnail;
}

class_alias(ThumbnailStrategy::class, 'eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy');
