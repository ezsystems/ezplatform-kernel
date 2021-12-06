<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\TrashServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeDeleteTrashItemEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeEmptyTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeRecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\DeleteTrashItemEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\EmptyTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\TrashService as TrashServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TrashService extends TrashServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        TrashServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function trash(Location $location): ?TrashItem
    {
        $eventData = [$location];

        $beforeEvent = new BeforeTrashEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResult();
        }

        $trashItem = $beforeEvent->isResultSet()
            ? $beforeEvent->getResult()
            : $this->innerService->trash($location);

        $this->eventDispatcher->dispatch(
            new TrashEvent($trashItem, ...$eventData)
        );

        return $trashItem;
    }

    public function recover(
        TrashItem $trashItem,
        ?Location $newParentLocation = null
    ): Location {
        $eventData = [
            $trashItem,
            $newParentLocation,
        ];

        $beforeEvent = new BeforeRecoverEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getLocation();
        }

        $location = $beforeEvent->hasLocation()
            ? $beforeEvent->getLocation()
            : $this->innerService->recover($trashItem, $newParentLocation);

        $this->eventDispatcher->dispatch(
            new RecoverEvent($location, ...$eventData)
        );

        return $location;
    }

    public function emptyTrash(): TrashItemDeleteResultList
    {
        $beforeEvent = new BeforeEmptyTrashEvent();

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultList();
        }

        $resultList = $beforeEvent->hasResultList()
            ? $beforeEvent->getResultList()
            : $this->innerService->emptyTrash();

        $this->eventDispatcher->dispatch(
            new EmptyTrashEvent($resultList)
        );

        return $resultList;
    }

    public function deleteTrashItem(TrashItem $trashItem): TrashItemDeleteResult
    {
        $eventData = [$trashItem];

        $beforeEvent = new BeforeDeleteTrashItemEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResult();
        }

        $result = $beforeEvent->hasResult()
            ? $beforeEvent->getResult()
            : $this->innerService->deleteTrashItem($trashItem);

        $this->eventDispatcher->dispatch(
            new DeleteTrashItemEvent($result, ...$eventData)
        );

        return $result;
    }
}

class_alias(TrashService::class, 'eZ\Publish\Core\Event\TrashService');
