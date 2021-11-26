<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\TrashService as TrashServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;

/**
 * TrashService for SiteAccessAware layer.
 *
 * Currently does nothing but hand over calls to aggregated service.
 */
class TrashService implements TrashServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\TrashService */
    protected $service;

    /**
     * Construct service object from aggregated service.
     *
     * @param \Ibexa\Contracts\Core\Repository\TrashService $service
     */
    public function __construct(
        TrashServiceInterface $service
    ) {
        $this->service = $service;
    }

    public function loadTrashItem(int $trashItemId): TrashItem
    {
        return $this->service->loadTrashItem($trashItemId);
    }

    public function trash(Location $location): ?TrashItem
    {
        return $this->service->trash($location);
    }

    public function recover(TrashItem $trashItem, Location $newParentLocation = null): Location
    {
        return $this->service->recover($trashItem, $newParentLocation);
    }

    public function emptyTrash(): TrashItemDeleteResultList
    {
        return $this->service->emptyTrash();
    }

    public function deleteTrashItem(TrashItem $trashItem): TrashItemDeleteResult
    {
        return $this->service->deleteTrashItem($trashItem);
    }

    public function findTrashItems(Query $query): SearchResult
    {
        return $this->service->findTrashItems($query);
    }
}

class_alias(TrashService::class, 'eZ\Publish\Core\Repository\SiteAccessAware\TrashService');
