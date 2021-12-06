<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Location;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct;

final class UpdateLocationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $updatedLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct */
    private $locationUpdateStruct;

    public function __construct(
        Location $updatedLocation,
        Location $location,
        LocationUpdateStruct $locationUpdateStruct
    ) {
        $this->updatedLocation = $updatedLocation;
        $this->location = $location;
        $this->locationUpdateStruct = $locationUpdateStruct;
    }

    public function getUpdatedLocation(): Location
    {
        return $this->updatedLocation;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getLocationUpdateStruct(): LocationUpdateStruct
    {
        return $this->locationUpdateStruct;
    }
}

class_alias(UpdateLocationEvent::class, 'eZ\Publish\API\Repository\Events\Location\UpdateLocationEvent');
