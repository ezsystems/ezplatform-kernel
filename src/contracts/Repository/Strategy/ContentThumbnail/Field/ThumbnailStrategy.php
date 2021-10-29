<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

interface ThumbnailStrategy
{
    public function getThumbnail(Field $field, ?VersionInfo $versionInfo = null): ?Thumbnail;
}

class_alias(ThumbnailStrategy::class, 'eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy');
