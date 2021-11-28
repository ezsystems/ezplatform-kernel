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

final class ThumbnailChainStrategy implements ThumbnailStrategy
{
    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy[] */
    private $strategies;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy[] $strategies
     */
    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function getThumbnail(ContentType $contentType, array $fields, ?VersionInfo $versionInfo = null): ?Thumbnail
    {
        foreach ($this->strategies as $strategy) {
            $thumbnail = $strategy->getThumbnail($contentType, $fields, $versionInfo);

            if ($thumbnail !== null) {
                return $thumbnail;
            }
        }

        return null;
    }
}

class_alias(ThumbnailChainStrategy::class, 'eZ\Publish\Core\Repository\Strategy\ContentThumbnail\ThumbnailChainStrategy');
