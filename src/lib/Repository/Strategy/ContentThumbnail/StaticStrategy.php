<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Strategy\ContentThumbnail;

use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class StaticStrategy implements ThumbnailStrategy
{
    /** @var string */
    private $staticThumbnail;

    public function __construct(string $staticThumbnail)
    {
        $this->staticThumbnail = $staticThumbnail;
    }

    public function getThumbnail(ContentType $contentType, array $fields, ?VersionInfo $versionInfo = null): Thumbnail
    {
        return new Thumbnail([
            'resource' => $this->staticThumbnail,
        ]);
    }
}

class_alias(StaticStrategy::class, 'eZ\Publish\Core\Repository\Strategy\ContentThumbnail\StaticStrategy');
