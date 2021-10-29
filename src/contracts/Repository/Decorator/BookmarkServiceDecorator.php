<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\BookmarkService;
use Ibexa\Contracts\Core\Repository\Values\Bookmark\BookmarkList;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

abstract class BookmarkServiceDecorator implements BookmarkService
{
    /** @var \Ibexa\Contracts\Core\Repository\BookmarkService */
    protected $innerService;

    public function __construct(BookmarkService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createBookmark(Location $location): void
    {
        $this->innerService->createBookmark($location);
    }

    public function deleteBookmark(Location $location): void
    {
        $this->innerService->deleteBookmark($location);
    }

    public function loadBookmarks(
        int $offset = 0,
        int $limit = 25
    ): BookmarkList {
        return $this->innerService->loadBookmarks($offset, $limit);
    }

    public function isBookmarked(Location $location): bool
    {
        return $this->innerService->isBookmarked($location);
    }
}

class_alias(BookmarkServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\BookmarkServiceDecorator');
