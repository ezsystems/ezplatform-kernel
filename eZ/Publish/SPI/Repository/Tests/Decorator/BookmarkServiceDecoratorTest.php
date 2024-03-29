<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Repository\Tests\Decorator;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\SPI\Repository\Decorator\BookmarkServiceDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookmarkServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): BookmarkService
    {
        return new class($service) extends BookmarkServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(BookmarkService::class);
    }

    public function testCreateBookmarkDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Location::class)];

        $serviceMock->expects($this->once())->method('createBookmark')->with(...$parameters);

        $decoratedService->createBookmark(...$parameters);
    }

    public function testDeleteBookmarkDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Location::class)];

        $serviceMock->expects($this->once())->method('deleteBookmark')->with(...$parameters);

        $decoratedService->deleteBookmark(...$parameters);
    }

    public function testLoadBookmarksDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            679,
            606,
        ];

        $serviceMock->expects($this->once())->method('loadBookmarks')->with(...$parameters);

        $decoratedService->loadBookmarks(...$parameters);
    }

    public function testIsBookmarkedDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Location::class)];

        $serviceMock->expects($this->once())->method('isBookmarked')->with(...$parameters);

        $decoratedService->isBookmarked(...$parameters);
    }
}
