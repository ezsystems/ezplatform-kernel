<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use Exception;
use Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark;
use Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Bookmark\Handler as BookmarkHandler;
use Ibexa\Contracts\Core\Repository\BookmarkService as BookmarkServiceInterface;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\Values\Bookmark\BookmarkList;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class BookmarkService implements BookmarkServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Contracts\Core\Persistence\Bookmark\Handler */
    protected $bookmarkHandler;

    /**
     * BookmarkService constructor.
     *
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\Contracts\Core\Persistence\Bookmark\Handler $bookmarkHandler
     */
    public function __construct(RepositoryInterface $repository, BookmarkHandler $bookmarkHandler)
    {
        $this->repository = $repository;
        $this->bookmarkHandler = $bookmarkHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function createBookmark(Location $location): void
    {
        $loadedLocation = $this->repository->getLocationService()->loadLocation($location->id);

        if ($this->isBookmarked($loadedLocation)) {
            throw new InvalidArgumentException('$location', 'Location is already bookmarked.');
        }

        $createStruct = new CreateStruct();
        $createStruct->name = $loadedLocation->contentInfo->name;
        $createStruct->locationId = $loadedLocation->id;
        $createStruct->userId = $this->getCurrentUserId();

        $this->repository->beginTransaction();
        try {
            $this->bookmarkHandler->create($createStruct);
            $this->repository->commit();
        } catch (Exception $ex) {
            $this->repository->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBookmark(Location $location): void
    {
        $loadedLocation = $this->repository->getLocationService()->loadLocation($location->id);

        $bookmarks = $this->bookmarkHandler->loadByUserIdAndLocationId(
            $this->getCurrentUserId(),
            [$loadedLocation->id]
        );

        if (empty($bookmarks)) {
            throw new InvalidArgumentException('$location', 'Location is not bookmarked.');
        }

        $this->repository->beginTransaction();
        try {
            $this->bookmarkHandler->delete(reset($bookmarks)->id);
            $this->repository->commit();
        } catch (Exception $ex) {
            $this->repository->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadBookmarks(int $offset = 0, int $limit = 25): BookmarkList
    {
        $currentUserId = $this->getCurrentUserId();

        $list = new BookmarkList();
        $list->totalCount = $this->bookmarkHandler->countUserBookmarks($currentUserId);
        if ($list->totalCount > 0) {
            $bookmarks = $this->bookmarkHandler->loadUserBookmarks($currentUserId, $offset, $limit);

            $list->items = array_map(function (Bookmark $bookmark) {
                return $this->repository->getLocationService()->loadLocation($bookmark->locationId);
            }, $bookmarks);
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function isBookmarked(Location $location): bool
    {
        $bookmarks = $this->bookmarkHandler->loadByUserIdAndLocationId(
            $this->getCurrentUserId(),
            [$location->id]
        );

        return !empty($bookmarks);
    }

    private function getCurrentUserId(): int
    {
        return $this->repository
            ->getPermissionResolver()
            ->getCurrentUserReference()
            ->getUserId();
    }
}

class_alias(BookmarkService::class, 'eZ\Publish\Core\Repository\BookmarkService');
