<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use UnexpectedValueException;

final class BeforeHideLocationEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location */
    private $location;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $hiddenLocation;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getHiddenLocation(): Location
    {
        if (!$this->hasHiddenLocation()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasHiddenLocation() or set it using setHiddenLocation() before you call the getter.', Location::class));
        }

        return $this->hiddenLocation;
    }

    public function setHiddenLocation(?Location $hiddenLocation): void
    {
        $this->hiddenLocation = $hiddenLocation;
    }

    public function hasHiddenLocation(): bool
    {
        return $this->hiddenLocation instanceof Location;
    }
}
