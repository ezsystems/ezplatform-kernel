<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\BackgroundIndexer;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Core\Search\Common\BackgroundIndexer as BackgroundIndexerInterface;

/**
 * Null indexer, does nothing, for default use when non has been configured.
 */
class NullIndexer implements BackgroundIndexerInterface
{
    public function registerContent(ContentInfo $contentInfo)
    {
    }

    public function registerLocation(Location $location)
    {
    }
}

class_alias(NullIndexer::class, 'eZ\Publish\Core\Search\Common\BackgroundIndexer\NullIndexer');
