<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Location;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

final class UnhideLocationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $revealedLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
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

class_alias(UnhideLocationEvent::class, 'eZ\Publish\API\Repository\Events\Location\UnhideLocationEvent');
