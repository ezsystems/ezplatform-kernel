<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationList;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;

/**
 * Location service, used for complex subtree operations.
 */
interface LocationService
{
    /**
     * Copies the subtree starting from $subtree as a new subtree of $targetLocation.
     *
     * Only the items on which the user has read access are copied.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed copy the subtree to the given parent location
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user does not have read access to the whole source subtree
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the target location is a sub location of the given location
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $subtree - the subtree denoted by the location to copy
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $targetParentLocation - the target parent location for the copy operation
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location The newly created location of the copied subtree
     */
    public function copySubtree(Location $subtree, Location $targetParentLocation): Location;

    /**
     * Loads a location object from its $locationId.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read this location
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If the specified location is not found
     *
     * @param mixed $locationId
     * @param string[]|null $prioritizedLanguages Filter on and use as prioritized language code on translated properties of returned object.
     * @param bool|null $useAlwaysAvailable Respect always available flag on content when filtering on $prioritizedLanguages.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function loadLocation(int $locationId, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null): Location;

    /**
     * Loads several location objects from its $locationIds.
     *
     * Returned list of Locations will be filtered by what is found and what current user has access to.
     *
     * @param array $locationIds
     * @param string[]|null $prioritizedLanguages Filter on and use as prioritized language code on translated properties of returned objects.
     * @param bool|null $useAlwaysAvailable Respect always available flag on content when filtering on $prioritizedLanguages.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]|iterable
     */
    public function loadLocationList(array $locationIds, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null): iterable;

    /**
     * Loads a location object from its $remoteId.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read this location
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If the specified location is not found
     *
     * @param string $remoteId
     * @param string[]|null $prioritizedLanguages Filter on and use as prioritized language code on translated properties of returned object.
     * @param bool|null $useAlwaysAvailable Respect always available flag on content when filtering on $prioritizedLanguages.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function loadLocationByRemoteId(string $remoteId, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null): Location;

    /**
     * Loads the locations for the given content object.
     *
     * If a $rootLocation is given, only locations that belong to this location are returned.
     * The location list is also filtered by permissions on reading locations.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException if there is no published version yet
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $rootLocation
     * @param string[]|null $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[] An array of {@link Location}
     */
    public function loadLocations(ContentInfo $contentInfo, ?Location $rootLocation = null, ?array $prioritizedLanguages = null): iterable;

    /**
     * Loads children which are readable by the current user of a location object sorted by sortField and sortOrder.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param int $offset the start offset for paging
     * @param int $limit the number of locations returned
     * @param string[]|null $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationList
     */
    public function loadLocationChildren(Location $location, int $offset = 0, int $limit = 25, ?array $prioritizedLanguages = null): LocationList;

    /**
     * Load parent Locations for Content Draft.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param string[]|null $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[] List of parent Locations
     */
    public function loadParentLocationsForDraftContent(VersionInfo $versionInfo, ?array $prioritizedLanguages = null): iterable;

    /**
     * Returns the number of children which are readable by the current user of a location object.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return int
     */
    public function getLocationChildCount(Location $location): int;

    /**
     * Creates the new $location in the content repository for the given content.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to create this location
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the content is already below the specified parent
     *                                        or the parent is a sub location of the location of the content
     *                                        or if set the remoteId exists already
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct $locationCreateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location the newly created Location
     */
    public function createLocation(ContentInfo $contentInfo, LocationCreateStruct $locationCreateStruct): Location;

    /**
     * Updates $location in the content repository.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to update this location
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException   if if set the remoteId exists already
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct $locationUpdateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location the updated Location
     */
    public function updateLocation(Location $location, LocationUpdateStruct $locationUpdateStruct): Location;

    /**
     * Swaps the contents held by $location1 and $location2.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to swap content
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2
     */
    public function swapLocation(Location $location1, Location $location2): void;

    /**
     * Hides the $location and marks invisible all descendants of $location.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to hide this location
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location $location, with updated hidden value
     */
    public function hideLocation(Location $location): Location;

    /**
     * Unhides the $location.
     *
     * This method and marks visible all descendants of $locations
     * until a hidden location is found.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to unhide this location
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location $location, with updated hidden value
     */
    public function unhideLocation(Location $location): Location;

    /**
     * Moves the subtree to $newParentLocation.
     *
     * If a user has the permission to move the location to a target location
     * he can do it regardless of an existing descendant on which the user has no permission.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to move this location to the target
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user does not have read access to the whole source subtree
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the new parent is in a subtree of the location
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the new parent location is the same as current
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the new parent location is not a container
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $newParentLocation
     */
    public function moveSubtree(Location $location, Location $newParentLocation): void;

    /**
     * Deletes $location and all its descendants.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user is not allowed to delete this location or a descendant
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    public function deleteLocation(Location $location): void;

    /**
     * Instantiates a new location create class.
     *
     * @param mixed $parentLocationId the parent under which the new location should be created
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct
     */
    public function newLocationCreateStruct(int $parentLocationId): LocationCreateStruct;

    /**
     * Instantiates a new location update class.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct
     */
    public function newLocationUpdateStruct(): LocationUpdateStruct;

    /**
     * Get the total number of all existing Locations. Can be combined with loadAllLocations.
     *
     * @see loadAllLocations
     *
     * @return int Total number of Locations
     */
    public function getAllLocationsCount(): int;

    /**
     * Bulk-load all existing Locations, constrained by $limit and $offset to paginate results.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function loadAllLocations(int $offset = 0, int $limit = 25): array;

    /**
     * Fetch a LocationList from the Repository filtered by the given conditions.
     *
     * @param string[] $languages a list of language codes to be added as additional constraints.
     *        If skipped, by default, unless SiteAccessAware layer has been disabled, languages set
     *        for a SiteAccess in a current context will be used.
     */
    public function find(Filter $filter, ?array $languages = null): LocationList;
}

class_alias(LocationService::class, 'eZ\Publish\API\Repository\LocationService');
