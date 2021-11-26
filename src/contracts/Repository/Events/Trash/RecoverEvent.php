<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Trash;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;

final class RecoverEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\TrashItem */
    private $trashItem;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $newParentLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    public function __construct(
        Location $location,
        TrashItem $trashItem,
        ?Location $newParentLocation = null
    ) {
        $this->trashItem = $trashItem;
        $this->newParentLocation = $newParentLocation;
        $this->location = $location;
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
        return $this->location;
    }
}

class_alias(RecoverEvent::class, 'eZ\Publish\API\Repository\Events\Trash\RecoverEvent');
