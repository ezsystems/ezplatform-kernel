<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Location;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

final class SwapLocationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location1;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location2;

    public function __construct(
        Location $location1,
        Location $location2
    ) {
        $this->location1 = $location1;
        $this->location2 = $location2;
    }

    public function getLocation1(): Location
    {
        return $this->location1;
    }

    public function getLocation2(): Location
    {
        return $this->location2;
    }
}

class_alias(SwapLocationEvent::class, 'eZ\Publish\API\Repository\Events\Location\SwapLocationEvent');
