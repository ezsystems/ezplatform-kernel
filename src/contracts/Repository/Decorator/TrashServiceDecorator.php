<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\TrashService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;

abstract class TrashServiceDecorator implements TrashService
{
    /** @var \Ibexa\Contracts\Core\Repository\TrashService */
    protected $innerService;

    public function __construct(TrashService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function loadTrashItem(int $trashItemId): TrashItem
    {
        return $this->innerService->loadTrashItem($trashItemId);
    }

    public function trash(Location $location): ?TrashItem
    {
        return $this->innerService->trash($location);
    }

    public function recover(
        TrashItem $trashItem,
        Location $newParentLocation = null
    ): Location {
        return $this->innerService->recover($trashItem, $newParentLocation);
    }

    public function emptyTrash(): TrashItemDeleteResultList
    {
        return $this->innerService->emptyTrash();
    }

    public function deleteTrashItem(TrashItem $trashItem): TrashItemDeleteResult
    {
        return $this->innerService->deleteTrashItem($trashItem);
    }

    public function findTrashItems(Query $query): SearchResult
    {
        return $this->innerService->findTrashItems($query);
    }
}

class_alias(TrashServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\TrashServiceDecorator');
