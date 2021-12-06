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

final class BeforeCopySubtreeEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $subtree;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $targetParentLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $location;

    public function __construct(Location $subtree, Location $targetParentLocation)
    {
        $this->subtree = $subtree;
        $this->targetParentLocation = $targetParentLocation;
    }

    public function getSubtree(): Location
    {
        return $this->subtree;
    }

    public function getTargetParentLocation(): Location
    {
        return $this->targetParentLocation;
    }

    public function getLocation(): Location
    {
        if (!$this->hasLocation()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasLocation() or set it using setLocation() before you call the getter.', Location::class));
        }

        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    public function hasLocation(): bool
    {
        return $this->location instanceof Location;
    }
}

class_alias(BeforeCopySubtreeEvent::class, 'eZ\Publish\API\Repository\Events\Location\BeforeCopySubtreeEvent');
