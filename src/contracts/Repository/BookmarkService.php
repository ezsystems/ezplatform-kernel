<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

use Ibexa\Contracts\Core\Repository\Values\Bookmark\BookmarkList;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * Bookmark Service.
 *
 * Service to handle bookmarking of Content item Locations. It works in the context of a current User (obtained from
 * the PermissionResolver).
 */
interface BookmarkService
{
    /**
     * Add location to bookmarks.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException When location is already bookmarked
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to create bookmark
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function createBookmark(Location $location): void;

    /**
     * Delete given location from bookmarks.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException When location is not bookmarked
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the current user user is not allowed to delete bookmark
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function deleteBookmark(Location $location): void;

    /**
     * List bookmarked locations.
     *
     * @param int $offset the start offset for paging
     * @param int $limit the number of bookmarked locations returned
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Bookmark\BookmarkList
     */
    public function loadBookmarks(int $offset = 0, int $limit = 25): BookmarkList;

    /**
     * Return true if location is bookmarked.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function isBookmarked(Location $location): bool;
}

class_alias(BookmarkService::class, 'eZ\Publish\API\Repository\BookmarkService');
