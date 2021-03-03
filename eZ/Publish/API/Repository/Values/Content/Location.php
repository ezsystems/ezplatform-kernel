<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content;

use eZ\Publish\API\Repository\Values\Location\Location as BaseLocation;

/**
 * This class represents a location in the repository.
 *
 * @property-read \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo calls getContentInfo()
 * @property-read int $contentId calls getContentInfo()->id
 */
abstract class Location extends BaseLocation
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Content */
    protected $content;

    /**
     * Returns the content info of the content object of this location.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    abstract public function getContentInfo(): ContentInfo;

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }
}
