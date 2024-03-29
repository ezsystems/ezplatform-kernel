<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Trash;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\TrashItem;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class RecoverEvent extends AfterEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Content\TrashItem */
    private $trashItem;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location */
    private $newParentLocation;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location */
    private $location;

    public function __construct(
        Location $location,
        TrashItem $trashItem,
        ?Location $newParentLocation = null
    ) {
        $this->trashItem = $trashItem;
        $this->newParentLocation = $newParentLocation;
        $this->location = $location;
    }

    public function getTrashItem(): TrashItem
    {
        return $this->trashItem;
    }

    public function getNewParentLocation(): ?Location
    {
        return $this->newParentLocation;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
