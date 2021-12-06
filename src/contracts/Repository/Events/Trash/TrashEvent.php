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

final class TrashEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\TrashItem|null */
    private $trashItem;

    public function __construct(
        ?TrashItem $trashItem,
        Location $location
    ) {
        $this->location = $location;
        $this->trashItem = $trashItem;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getTrashItem(): ?TrashItem
    {
        return $this->trashItem;
    }
}

class_alias(TrashEvent::class, 'eZ\Publish\API\Repository\Events\Trash\TrashEvent');
