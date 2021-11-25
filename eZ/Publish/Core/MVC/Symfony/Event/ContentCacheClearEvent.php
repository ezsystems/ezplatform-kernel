<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Event;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ContentCacheClearEvent.
 *
 * @deprecated Since 6.12, not triggered anymore when using ezplatform-http-cache, as this never worked for for instance
 * deleted content.
 */
class ContentCacheClearEvent extends Event
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location[] */
    private $locationsToClear = [];

    public function __construct(ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * Returns ContentInfo object we're clearing the cache for.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    public function getContentInfo()
    {
        return $this->contentInfo;
    }

    /**
     * Returns all location objects registered to the cache clear process.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getLocationsToClear()
    {
        return $this->locationsToClear;
    }

    /**
     * Adds a location that needs to be cleared.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    public function addLocationToClear(Location $location)
    {
        $this->locationsToClear[] = $location;
    }

    /**
     * Replaces the list of locations to clear.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locationsToClear
     */
    public function setLocationsToClear(array $locationsToClear)
    {
        $this->locationsToClear = $locationsToClear;
    }
}
