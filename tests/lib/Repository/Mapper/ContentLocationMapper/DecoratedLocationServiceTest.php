<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Mapper\ContentLocationMapper;

use Ibexa\Contracts\Core\Repository\LocationService as ApiLocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationList;
use Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper;
use Ibexa\Core\Repository\Mapper\ContentLocationMapper\DecoratedLocationService;
use Ibexa\Core\Repository\Values\Content\Location;
use PHPUnit\Framework\TestCase;

class DecoratedLocationServiceTest extends TestCase
{
    /** @var \Ibexa\Core\Repository\Mapper\ContentLocationMapper\DecoratedLocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $innerLocationService;

    /** @var \Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createMock(ContentLocationMapper::class);
        $this->innerLocationService = $this->createMock(ApiLocationService::class);

        $this->locationService = new DecoratedLocationService($this->innerLocationService, $this->mapper);
    }

    public function testLoadLocation(): void
    {
        $location = new Location([
            'id' => 1,
            'contentInfo' => new ContentInfo([
                'id' => 2,
            ]),
        ]);

        $this->innerLocationService
            ->method('loadLocation')
            ->willReturn($location);

        $this->mapper
            ->expects($this->once())
            ->method('setMapping')
            ->with(1, 2);

        $actualLocation = $this->locationService->loadLocation(1);
        self::assertInstanceOf(Location::class, $actualLocation);
        self::assertEquals(1, $actualLocation->id);
        self::assertEquals(2, $actualLocation->contentId);
    }

    public function testLoadLocationList(): void
    {
        $locations = new LocationList([
            'locations' => [
                new Location([
                    'id' => 1,
                    'contentInfo' => new ContentInfo([
                        'id' => 2,
                    ]),
                ]),
                new Location([
                    'id' => 3,
                    'contentInfo' => new ContentInfo([
                        'id' => 4,
                    ]),
                ]),
            ],
        ]);

        $this->innerLocationService
            ->method('loadLocationList')
            ->willReturn($locations);

        $this->mapper
            ->expects($this->atLeastOnce())
            ->method('setMapping')
            ->withConsecutive([1, 2], [3, 4]);

        $actualLocations = $this->locationService->loadLocationList([1, 2]);

        $location1 = $actualLocations->locations[0];
        self::assertInstanceOf(Location::class, $location1);
        self::assertEquals(1, $location1->id);
        self::assertEquals(2, $location1->contentId);

        $location2 = $actualLocations->locations[1];
        self::assertInstanceOf(Location::class, $location2);
        self::assertEquals(3, $location2->id);
        self::assertEquals(4, $location2->contentId);
    }

    public function testLoadLocations(): void
    {
        $contentInfo = new ContentInfo([
            'id' => 1,
        ]);
        $locations = [
            new Location([
                'id' => 1,
                'contentInfo' => new ContentInfo([
                    'id' => 2,
                ]),
            ]),
            new Location([
                'id' => 3,
                'contentInfo' => new ContentInfo([
                    'id' => 4,
                ]),
            ]),
        ];

        $this->innerLocationService
            ->method('loadLocations')
            ->with($contentInfo)
            ->willReturn($locations);

        $this->mapper
            ->expects($this->atLeastOnce())
            ->method('setMapping')
            ->withConsecutive([1, 2], [3, 4]);

        $actualLocations = $this->locationService->loadLocations($contentInfo);

        $location1 = $actualLocations[0];
        self::assertInstanceOf(Location::class, $location1);
        self::assertEquals(1, $location1->id);
        self::assertEquals(2, $location1->contentId);

        $location2 = $actualLocations[1];
        self::assertInstanceOf(Location::class, $location2);
        self::assertEquals(3, $location2->id);
        self::assertEquals(4, $location2->contentId);
    }

    public function testLoadLocationChildren(): void
    {
        $location = new Location([
            'id' => 5,
        ]);
        $locationList = new LocationList([
            'locations' => [
                new Location([
                    'id' => 1,
                    'contentInfo' => new ContentInfo([
                        'id' => 2,
                    ]),
                ]),
                new Location([
                    'id' => 3,
                    'contentInfo' => new ContentInfo([
                        'id' => 4,
                    ]),
                ]),
            ],
        ]);

        $this->innerLocationService
            ->method('loadLocationChildren')
            ->with($location)
            ->willReturn($locationList);

        $this->mapper
            ->expects($this->atLeastOnce())
            ->method('setMapping')
            ->withConsecutive([1, 2], [3, 4]);

        $actualLocations = $this->locationService->loadLocationChildren($location);

        $location1 = $actualLocations->locations[0];
        self::assertInstanceOf(Location::class, $location1);
        self::assertEquals(1, $location1->id);
        self::assertEquals(2, $location1->contentId);

        $location2 = $actualLocations->locations[1];
        self::assertInstanceOf(Location::class, $location2);
        self::assertEquals(3, $location2->id);
        self::assertEquals(4, $location2->contentId);
    }

    public function testLoadAllLocations(): void
    {
        $locations = [
            new Location([
                'id' => 1,
                'contentInfo' => new ContentInfo([
                    'id' => 2,
                ]),
            ]),
            new Location([
                'id' => 3,
                'contentInfo' => new ContentInfo([
                    'id' => 4,
                ]),
            ]),
        ];

        $this->innerLocationService
            ->method('loadAllLocations')
            ->willReturn($locations);

        $this->mapper
            ->expects($this->atLeastOnce())
            ->method('setMapping')
            ->withConsecutive([1, 2], [3, 4]);

        $actualLocations = $this->locationService->loadAllLocations();

        $location1 = $actualLocations[0];
        self::assertInstanceOf(Location::class, $location1);
        self::assertEquals(1, $location1->id);
        self::assertEquals(2, $location1->contentId);

        $location2 = $actualLocations[1];
        self::assertInstanceOf(Location::class, $location2);
        self::assertEquals(3, $location2->id);
        self::assertEquals(4, $location2->contentId);
    }
}
