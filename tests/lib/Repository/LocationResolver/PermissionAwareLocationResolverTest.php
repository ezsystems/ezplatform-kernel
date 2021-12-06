<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\LocationResolver;

use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\LocationResolver\PermissionAwareLocationResolver;
use Ibexa\Core\Repository\Values\Content\Location;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Repository\LocationResolver\PermissionAwareLocationResolver
 */
final class PermissionAwareLocationResolverTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Core\Repository\LocationResolver\LocationResolver */
    private $locationResolver;

    public function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);

        $this->locationResolver = new PermissionAwareLocationResolver($this->locationService);
    }

    public function testResolveMainLocation(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42]);
        $location = new Location(['id' => 42]);

        // User has access to the main Location
        $this->locationService
            ->method('loadLocation')
            ->willReturn($location);

        $this->assertSame($location, $this->locationResolver->resolveLocation($contentInfo));
    }

    /**
     * Test for the resolveLocation() method.
     */
    public function testResolveSecondaryLocation(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42]);
        $location1 = new Location(['id' => 43]);
        $location2 = new Location(['id' => 44]);

        // User doesn't have access to main location but to the third Content's location
        $this->locationService
            ->method('loadLocation')
            ->willThrowException($this->createMock(UnauthorizedException::class));

        $this->locationService
            ->method('loadLocations')
            ->willReturn([$location1, $location2]);

        $this->assertSame($location1, $this->locationResolver->resolveLocation($contentInfo));
    }

    /**
     * Test for the resolveLocation() method when Locations don't exist.
     */
    public function testExpectNotFoundExceptionWhenLocationDoesNotExist(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42]);

        $this->locationService
            ->method('loadLocation')
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->locationService
            ->method('loadLocations')
            ->willReturn([]);

        $this->expectException(NotFoundException::class);

        $this->locationResolver->resolveLocation($contentInfo);
    }

    /**
     * Test for the resolveLocation() method when ContentInfo's mainLocationId is null.
     */
    public function testExpectNotFoundExceptionWhenMainLocationIdIsNull(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => null]);

        $this->expectException(NotFoundException::class);

        $this->locationResolver->resolveLocation($contentInfo);
    }

    /**
     * Test for the resolveLocation() method when Location is not yet published.
     */
    public function testExpectBadStateExceptionWhenContentNotYetPublished(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42, 'status' => ContentInfo::STATUS_DRAFT]);

        $this->locationService
            ->method('loadLocation')
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->locationService
            ->method('loadLocations')
            ->willThrowException($this->createMock(BadStateException::class));

        $this->expectException(BadStateException::class);

        $this->locationResolver->resolveLocation($contentInfo);
    }
}

class_alias(PermissionAwareLocationResolverTest::class, 'eZ\Publish\Core\Repository\Tests\LocationResolver\PermissionAwareLocationResolverTest');
