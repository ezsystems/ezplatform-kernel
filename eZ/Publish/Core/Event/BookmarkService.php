<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event;

use eZ\Publish\API\Repository\BookmarkService as BookmarkServiceInterface;
use eZ\Publish\API\Repository\Events\Bookmark\BeforeCreateBookmarkEvent;
use eZ\Publish\API\Repository\Events\Bookmark\BeforeDeleteBookmarkEvent;
use eZ\Publish\API\Repository\Events\Bookmark\CreateBookmarkEvent;
use eZ\Publish\API\Repository\Events\Bookmark\DeleteBookmarkEvent;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\SPI\Repository\Decorator\BookmarkServiceDecorator;
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
