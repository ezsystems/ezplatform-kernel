<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Location;

use eZ\Publish\API\Repository\Values\Location\Location;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class MoveSubtreeEvent extends AfterEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Location\Location */
    private $location;

    /** @var \eZ\Publish\API\Repository\Values\Location\Location */
    private $newParentLocation;

    public function __construct(
        Location $location,
        Location $newParentLocation
    ) {
        $this->location = $location;
        $this->newParentLocation = $newParentLocation;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getNewParentLocation(): Location
    {
        return $this->newParentLocation;
    }
}
