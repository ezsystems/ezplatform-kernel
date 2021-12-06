<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\BookmarkService as BookmarkServiceInterface;
use Ibexa\Contracts\Core\Repository\Decorator\BookmarkServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Bookmark\BeforeCreateBookmarkEvent;
use Ibexa\Contracts\Core\Repository\Events\Bookmark\BeforeDeleteBookmarkEvent;
use Ibexa\Contracts\Core\Repository\Events\Bookmark\CreateBookmarkEvent;
use Ibexa\Contracts\Core\Repository\Events\Bookmark\DeleteBookmarkEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookmarkService extends BookmarkServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        BookmarkServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createBookmark(Location $location): void
    {
        $eventData = [$location];

        $beforeEvent = new BeforeCreateBookmarkEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->createBookmark($location);

        $this->eventDispatcher->dispatch(new CreateBookmarkEvent(...$eventData));
    }

    public function deleteBookmark(Location $location): void
    {
        $eventData = [$location];

        $beforeEvent = new BeforeDeleteBookmarkEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteBookmark($location);

        $this->eventDispatcher->dispatch(new DeleteBookmarkEvent(...$eventData));
    }
}

class_alias(BookmarkService::class, 'eZ\Publish\Core\Event\BookmarkService');
