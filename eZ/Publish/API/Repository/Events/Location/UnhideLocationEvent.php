<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Location;

use eZ\Publish\API\Repository\Values\Location\Location;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class UnhideLocationEvent extends AfterEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Location\Location */
    private $revealedLocation;

    /** @var \eZ\Publish\API\Repository\Values\Location\Location */
    private $location;

    public function __construct(
        Location $revealedLocation,
        Location $location
    ) {
        $this->revealedLocation = $revealedLocation;
        $this->location = $location;
    }

    public function getRevealedLocation(): Location
    {
        return $this->revealedLocation;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
