<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Location;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use UnexpectedValueException;

final class BeforeUnhideLocationEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $revealedLocation;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getRevealedLocation(): Location
    {
        if (!$this->hasRevealedLocation()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasRevealedLocation() or set it using setRevealedLocation() before you call the getter.', Location::class));
        }

        return $this->revealedLocation;
    }

    public function setRevealedLocation(?Location $revealedLocation): void
    {
        $this->revealedLocation = $revealedLocation;
    }

    public function hasRevealedLocation(): bool
    {
        return $this->revealedLocation instanceof Location;
    }
}

class_alias(BeforeUnhideLocationEvent::class, 'eZ\Publish\API\Repository\Events\Location\BeforeUnhideLocationEvent');
