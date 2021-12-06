<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Location;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateLocationEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct */
    private $locationUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $updatedLocation;

    public function __construct(Location $location, LocationUpdateStruct $locationUpdateStruct)
    {
        $this->location = $location;
        $this->locationUpdateStruct = $locationUpdateStruct;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getLocationUpdateStruct(): LocationUpdateStruct
    {
        return $this->locationUpdateStruct;
    }

    public function getUpdatedLocation(): Location
    {
        if (!$this->hasUpdatedLocation()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedLocation() or set it using setUpdatedLocation() before you call the getter.', Location::class));
        }

        return $this->updatedLocation;
    }

    public function setUpdatedLocation(?Location $updatedLocation): void
    {
        $this->updatedLocation = $updatedLocation;
    }

    public function hasUpdatedLocation(): bool
    {
        return $this->updatedLocation instanceof Location;
    }
}

class_alias(BeforeUpdateLocationEvent::class, 'eZ\Publish\API\Repository\Events\Location\BeforeUpdateLocationEvent');
