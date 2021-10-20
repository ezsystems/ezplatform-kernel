<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\ContentLocationMapper\Service;

use eZ\Publish\API\Repository\LocationService as RepositoryLocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationList;
use eZ\Publish\SPI\Repository\Decorator\LocationServiceDecorator;
use eZ\Publish\Core\Repository\ContentLocationMapper\ContentLocationMapper;

/**
 * Service decorator hooking ContentLocationMapper to load* calls.
 *
 * @internal
 */
final class LocationService extends LocationServiceDecorator
{
    /** @var \eZ\Publish\Core\Repository\ContentLocationMapper\ContentLocationMapper */
    private $contentLocationMapper;

    public function __construct(
        RepositoryLocationService $innerService,
        ContentLocationMapper $contentLocationMapper
    ) {
        parent::__construct($innerService);

        $this->contentLocationMapper = $contentLocationMapper;
    }

    public function loadLocation(
        int $locationId,
        ?array $prioritizedLanguages = null,
        ?bool $useAlwaysAvailable = null
    ): Location {
        $location = $this->innerService->loadLocation(
            $locationId,
            $prioritizedLanguages,
            $useAlwaysAvailable
        );

        $this->contentLocationMapper->setMapping(
            $locationId,
            $location->contentId
        );

        return $location;
    }

    public function loadLocationList(
        array $locationIds,
        ?array $prioritizedLanguages = null,
        ?bool $useAlwaysAvailable = null
    ): iterable {
        $locationList = $this->innerService->loadLocationList(
            $locationIds,
            $prioritizedLanguages,
            $useAlwaysAvailable
        );

        $this->setLocationMappings($locationList);

        return $locationList;
    }

    public function loadLocations(
        ContentInfo $contentInfo,
        ?Location $rootLocation = null,
        ?array $prioritizedLanguages = null
    ): iterable {
        $locations = $this->innerService->loadLocations(
            $contentInfo,
            $rootLocation,
            $prioritizedLanguages
        );

        $this->setLocationMappings($locations);

        return $locations;
    }

    public function loadLocationChildren(
        Location $location,
        int $offset = 0,
        int $limit = 25,
        ?array $prioritizedLanguages = null
    ): LocationList {
        $locationChildren = $this->innerService->loadLocationChildren(
            $location,
            $offset,
            $limit,
            $prioritizedLanguages
        );

        $this->setLocationMappings($locationChildren->locations);

        return $locationChildren;
    }

    public function loadAllLocations(
        int $offset = 0,
        int $limit = 25
    ): array {
        $locations = $this->innerService->loadAllLocations(
            $offset,
            $limit
        );

        $this->setLocationMappings($locations);

        return $locations;
    }

    /**
     * @param iterable<\eZ\Publish\API\Repository\Values\Content\Location>
     */
    private function setLocationMappings(iterable $locationList): void
    {
        foreach ($locationList as $location) {
            $this->contentLocationMapper->setMapping(
                $location->id,
                $location->contentId
            );
        }
    }
}
