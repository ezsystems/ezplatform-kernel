<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests;

use eZ\Publish\API\Repository\Values\Bookmark\BookmarkList;

/**
 * Test case for the BookmarkService.
 *
 * @see \eZ\Publish\API\Repository\BookmarkService
 */
class BookmarkServiceTest extends BaseTest
{
    public const LOCATION_ID_BOOKMARKED = 5;
    public const LOCATION_ID_NOT_BOOKMARKED = 44;

    /**
     * @covers \eZ\Publish\API\Repository\BookmarkService::isBookmarked
     */
    public function testIsBookmarked()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $location = $repository->getLocationService()->loadLocation($this->generateId('location', self::LOCATION_ID_BOOKMARKED));
        $isBookmarked = $repository->getBookmarkService()->isBookmarked($location);
        /* END: Use Case */

        $this->assertTrue($isBookmarked);
    }

    /**
     * @covers \eZ\Publish\API\Repository\BookmarkService::isBookmarked
     */
    public function testIsNotBookmarked()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $location = $repository->getLocationService()->loadLocation($this->generateId('location', self::LOCATION_ID_NOT_BOOKMARKED));
        $isBookmarked = $repository->getBookmarkService()->isBookmarked($location);
        /* END: Use Case */

        $this->assertFalse($isBookmarked);
    }

    /**
     * @covers \eZ\Publish\Core\Repository\BookmarkService::createBookmark
     */
    public function testCreateBookmark()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $bookmarkService = $repository->getBookmarkService();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation($this->generateId('location', self::LOCATION_ID_NOT_BOOKMARKED));
        $beforeCreateBookmark = $bookmarkService->isBookmarked($location);
        $bookmarkService->createBookmark($location);
        $afterCreateBookmark = $bookmarkService->isBookmarked($location);
        /* END: Use Case */

        $this->assertFalse($beforeCreateBookmark);
        $this->assertTrue($afterCreateBookmark);
    }

    /**
     * @covers \eZ\Publish\Core\Repository\BookmarkService::createBookmark
     * @depends testCreateBookmark
     */
    public function testCreateBookmarkThrowsInvalidArgumentException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $bookmarkService = $repository->getBookmarkService();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation($this->generateId('location', self::LOCATION_ID_BOOKMARKED));
        $bookmarkService->createBookmark($location);
        /* END: Use Case */
    }

    /**
     * @covers \eZ\Publish\Core\Repository\BookmarkService::deleteBookmark
     */
    public function testDeleteBookmark()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $bookmarkService = $repository->getBookmarkService();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation($this->generateId('location', self::LOCATION_ID_BOOKMARKED));

        $beforeDeleteBookmark = $bookmarkService->isBookmarked($location);
        $bookmarkService->deleteBookmark($location);
        $afterDeleteBookmark = $bookmarkService->isBookmarked($location);
        /* END: Use Case */

        $this->assertTrue($beforeDeleteBookmark);
        $this->assertFalse($afterDeleteBookmark);
    }

    /**
     * @covers \eZ\Publish\Core\Repository\BookmarkService::deleteBookmark
     * @depends testDeleteBookmark
     */
    public function testDeleteBookmarkThrowsInvalidArgumentException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $bookmarkService = $repository->getBookmarkService();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation($this->generateId('location', self::LOCATION_ID_NOT_BOOKMARKED));
        $bookmarkService->deleteBookmark($location);
        /* END: Use Case */
    }

    /**
     * @covers \eZ\Publish\Core\Repository\BookmarkService::loadBookmarks
     */
    public function testLoadBookmarks()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $bookmarks = $repository->getBookmarkService()->loadBookmarks(1, 3);
        /* END: Use Case */

        $this->assertInstanceOf(BookmarkList::class, $bookmarks);
        $this->assertEquals($bookmarks->totalCount, 5);
        // Assert bookmarks order: recently added should be first
        $this->assertEquals([15, 13, 12], array_map(static function ($location) {
            return $location->id;
        }, $bookmarks->items));
    }
}
