<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Location;

use eZ\Publish\API\Repository\Values\Location\Location;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;

final class BeforeDeleteLocationEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location */
    private $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
