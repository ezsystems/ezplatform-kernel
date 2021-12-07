<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Trash;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use UnexpectedValueException;

final class BeforeRecoverEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\TrashItem */
    private $trashItem;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $newParentLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $location;

    public function __construct(TrashItem $trashItem, ?Location $newParentLocation = null)
    {
        $this->trashItem = $trashItem;
        $this->newParentLocation = $newParentLocation;
    }

    public function getTrashItem(): TrashItem
    {
        return $this->trashItem;
    }

    public function getNewParentLocation(): ?Location
    {
        return $this->newParentLocation;
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

class_alias(BeforeRecoverEvent::class, 'eZ\Publish\API\Repository\Events\Trash\BeforeRecoverEvent');
