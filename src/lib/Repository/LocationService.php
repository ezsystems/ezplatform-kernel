<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use function count;
use Exception;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Persistence\Content\Location\UpdateStruct;
use Ibexa\Contracts\Core\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Handler;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService as LocationServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationList;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LanguageCode;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd as CriterionLogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot as CriterionLogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree as CriterionSubtree;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Repository\Mapper\ContentDomainMapper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Location service, used for complex subtree operations.
 *
 * @example Examples/location.php
 */
class LocationService implements LocationServiceInterface
{
    /** @var \Ibexa\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistenceHandler;

    /** @var array */
    protected $settings;

    /** @var \Ibexa\Core\Repository\Mapper\ContentDomainMapper */
    protected $contentDomainMapper;

    /** @var \Ibexa\Core\Repository\Helper\NameSchemaService */
    protected $nameSchemaService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver */
    protected $permissionCriterionResolver;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Persistence\Filter\Location\Handler */
    private $locationFilteringHandler;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * Setups service with reference to repository object that created it & corresponding handler.
     *
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\Contracts\Core\Persistence\Handler $handler
     * @param \Ibexa\Core\Repository\Mapper\ContentDomainMapper $contentDomainMapper
     * @param \Ibexa\Core\Repository\Helper\NameSchemaService $nameSchemaService
     * @param \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver $permissionCriterionResolver
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param array $settings
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        Handler $handler,
        ContentDomainMapper $contentDomainMapper,
        Helper\NameSchemaService $nameSchemaService,
        PermissionCriterionResolver $permissionCriterionResolver,
        PermissionResolver $permissionResolver,
        LocationFilteringHandler $locationFilteringHandler,
        ContentTypeService $contentTypeService,
        array $settings = [],
        LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->persistenceHandler = $handler;
        $this->contentDomainMapper = $contentDomainMapper;
        $this->nameSchemaService = $nameSchemaService;
        $this->permissionResolver = $permissionResolver;
        $this->locationFilteringHandler = $locationFilteringHandler;
        // Union makes sure default settings are ignored if provided in argument
        $this->settings = $settings + [
            //'defaultSetting' => array(),
        ];
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        $this->contentTypeService = $contentTypeService;
        $this->logger = null !== $logger ? $logger : new NullLogger();
    }

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
    public function copySubtree(APILocation $subtree, APILocation $targetParentLocation): APILocation
    {
        $loadedSubtree = $this->loadLocation($subtree->id);
        $loadedTargetLocation = $this->loadLocation($targetParentLocation->id);

        if (stripos($loadedTargetLocation->pathString, $loadedSubtree->pathString) !== false) {
            throw new InvalidArgumentException('targetParentLocation', 'Cannot copy subtree to its own descendant Location');
        }

        // check create permission on target
        if (!$this->permissionResolver->canUser('content', 'create', $loadedSubtree->getContentInfo(), [$loadedTargetLocation])) {
            throw new UnauthorizedException('content', 'create', ['locationId' => $loadedTargetLocation->id]);
        }

        // Check read access to whole source subtree
        $contentReadCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'read');
        if ($contentReadCriterion === false) {
            throw new UnauthorizedException('content', 'read');
        } elseif ($contentReadCriterion !== true) {
            // Query if there are any content in subtree current user don't have access to
            $query = new Query(
                [
                    'limit' => 0,
                    'filter' => new CriterionLogicalAnd(
                        [
                            new CriterionSubtree($loadedSubtree->pathString),
                            new CriterionLogicalNot($contentReadCriterion),
                        ]
                    ),
                ]
            );
            $result = $this->repository->getSearchService()->findContent($query, [], false);
            if ($result->totalCount > 0) {
                throw new UnauthorizedException('content', 'read');
            }
        }

        $this->repository->beginTransaction();
        try {
            $newLocation = $this->persistenceHandler->locationHandler()->copySubtree(
                $loadedSubtree->id,
                $loadedTargetLocation->id,
                $this->repository->getPermissionResolver()->getCurrentUserReference()->getUserId()
            );

            $content = $this->repository->getContentService()->loadContent($newLocation->contentId);
            $urlAliasNames = $this->nameSchemaService->resolveUrlAliasSchema($content);
            foreach ($urlAliasNames as $languageCode => $name) {
                $this->persistenceHandler->urlAliasHandler()->publishUrlAliasForLocation(
                    $newLocation->id,
                    $loadedTargetLocation->id,
                    $name,
                    $languageCode,
                    $content->contentInfo->alwaysAvailable
                );
            }

            $this->persistenceHandler->urlAliasHandler()->locationCopied(
                $loadedSubtree->id,
                $newLocation->id,
                $loadedTargetLocation->id
            );

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->contentDomainMapper->buildLocationWithContent($newLocation, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function loadLocation(int $locationId, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null): APILocation
    {
        $spiLocation = $this->persistenceHandler->locationHandler()->load($locationId, $prioritizedLanguages, $useAlwaysAvailable ?? true);
        $location = $this->contentDomainMapper->buildLocation($spiLocation, $prioritizedLanguages ?: [], $useAlwaysAvailable ?? true);
        if (!$this->permissionResolver->canUser('content', 'read', $location->getContentInfo(), [$location])) {
            throw new UnauthorizedException('content', 'read', ['locationId' => $location->id]);
        }

        return $location;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLocationList(array $locationIds, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null): iterable
    {
        $spiLocations = $this->persistenceHandler->locationHandler()->loadList(
            $locationIds,
            $prioritizedLanguages,
            $useAlwaysAvailable ?? true
        );
        if (empty($spiLocations)) {
            return [];
        }

        // Get content id's
        $contentIds = [];
        foreach ($spiLocations as $spiLocation) {
            $contentIds[] = $spiLocation->contentId;
        }

        // Load content info and Get content proxy
        $spiContentInfoList = $this->persistenceHandler->contentHandler()->loadContentInfoList($contentIds);
        $contentProxyList = $this->contentDomainMapper->buildContentProxyList(
            $spiContentInfoList,
            $prioritizedLanguages ?? [],
            $useAlwaysAvailable ?? true
        );

        // Build locations using the bulk retrieved content info and bulk lazy loaded content proxies.
        $locations = [];
        $permissionResolver = $this->repository->getPermissionResolver();
        foreach ($spiLocations as $spiLocation) {
            $location = $this->contentDomainMapper->buildLocationWithContent(
                $spiLocation,
                $contentProxyList[$spiLocation->contentId] ?? null,
                $spiContentInfoList[$spiLocation->contentId] ?? null
            );

            if ($permissionResolver->canUser('content', 'read', $location->getContentInfo(), [$location])) {
                $locations[$spiLocation->id] = $location;
            }
        }

        return $locations;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLocationByRemoteId(string $remoteId, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null): APILocation
    {
        $spiLocation = $this->persistenceHandler->locationHandler()->loadByRemoteId($remoteId, $prioritizedLanguages, $useAlwaysAvailable ?? true);
        $location = $this->contentDomainMapper->buildLocation($spiLocation, $prioritizedLanguages ?: [], $useAlwaysAvailable ?? true);
        if (!$this->permissionResolver->canUser('content', 'read', $location->getContentInfo(), [$location])) {
            throw new UnauthorizedException('content', 'read', ['locationId' => $location->id]);
        }

        return $location;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLocations(ContentInfo $contentInfo, ?APILocation $rootLocation = null, ?array $prioritizedLanguages = null): iterable
    {
        if (!$contentInfo->published) {
            throw new BadStateException('$contentInfo', 'The Content item has no published versions');
        }

        $spiLocations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $contentInfo->id,
            $rootLocation !== null ? $rootLocation->id : null
        );

        $locations = [];
        $spiInfo = $this->persistenceHandler->contentHandler()->loadContentInfo($contentInfo->id);
        $content = $this->contentDomainMapper->buildContentProxy($spiInfo, $prioritizedLanguages ?: []);
        foreach ($spiLocations as $spiLocation) {
            $location = $this->contentDomainMapper->buildLocationWithContent($spiLocation, $content, $spiInfo);
            if ($this->permissionResolver->canUser('content', 'read', $location->getContentInfo(), [$location])) {
                $locations[] = $location;
            }
        }

        return $locations;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLocationChildren(APILocation $location, int $offset = 0, int $limit = 25, ?array $prioritizedLanguages = null): LocationList
    {
        if (!$this->contentDomainMapper->isValidLocationSortField($location->sortField)) {
            throw new InvalidArgumentValue('sortField', $location->sortField, 'Location');
        }

        if (!$this->contentDomainMapper->isValidLocationSortOrder($location->sortOrder)) {
            throw new InvalidArgumentValue('sortOrder', $location->sortOrder, 'Location');
        }

        if (!is_int($offset)) {
            throw new InvalidArgumentValue('offset', $offset);
        }

        if (!is_int($limit)) {
            throw new InvalidArgumentValue('limit', $limit);
        }

        $childLocations = [];
        $searchResult = $this->searchChildrenLocations($location, $offset, $limit, $prioritizedLanguages ?: []);
        foreach ($searchResult->searchHits as $searchHit) {
            $childLocations[] = $searchHit->valueObject;
        }

        return new LocationList(
            [
                'locations' => $childLocations,
                'totalCount' => $searchResult->totalCount,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function loadParentLocationsForDraftContent(VersionInfo $versionInfo, ?array $prioritizedLanguages = null): iterable
    {
        if (!$versionInfo->isDraft()) {
            throw new BadStateException(
                '$contentInfo',
                sprintf(
                    'Content item [%d] %s is already published. Use LocationService::loadLocations instead.',
                    $versionInfo->contentInfo->id,
                    $versionInfo->contentInfo->name
                )
            );
        }

        $spiLocations = $this->persistenceHandler
            ->locationHandler()
            ->loadParentLocationsForDraftContent($versionInfo->contentInfo->id);

        $contentIds = [];
        foreach ($spiLocations as $spiLocation) {
            $contentIds[] = $spiLocation->contentId;
        }

        $locations = [];
        $permissionResolver = $this->repository->getPermissionResolver();
        $spiContentInfoList = $this->persistenceHandler->contentHandler()->loadContentInfoList($contentIds);
        $contentList = $this->contentDomainMapper->buildContentProxyList($spiContentInfoList, $prioritizedLanguages ?: []);
        foreach ($spiLocations as $spiLocation) {
            $location = $this->contentDomainMapper->buildLocationWithContent(
                $spiLocation,
                $contentList[$spiLocation->contentId],
                $spiContentInfoList[$spiLocation->contentId]
            );

            if ($permissionResolver->canUser('content', 'read', $location->getContentInfo(), [$location])) {
                $locations[] = $location;
            }
        }

        return $locations;
    }

    /**
     * Returns the number of children which are readable by the current user of a location object.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return int
     */
    public function getLocationChildCount(APILocation $location): int
    {
        $searchResult = $this->searchChildrenLocations($location, 0, 0);

        return $searchResult->totalCount;
    }

    /**
     * Searches children locations of the provided parent location id.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult
     */
    protected function searchChildrenLocations(APILocation $location, $offset = 0, $limit = -1, array $prioritizedLanguages = null)
    {
        $query = new LocationQuery([
            'filter' => new Criterion\ParentLocationId($location->id),
            'offset' => $offset >= 0 ? (int)$offset : 0,
            'limit' => $limit >= 0 ? (int)$limit : null,
            'sortClauses' => $location->getSortClauses(),
        ]);

        return $this->repository->getSearchService()->findLocations($query, ['languages' => $prioritizedLanguages]);
    }

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
    public function createLocation(ContentInfo $contentInfo, LocationCreateStruct $locationCreateStruct): APILocation
    {
        $content = $this->contentDomainMapper->buildContentDomainObjectFromPersistence(
            $this->persistenceHandler->contentHandler()->load($contentInfo->id),
            $this->persistenceHandler->contentTypeHandler()->load($contentInfo->contentTypeId)
        );

        $parentLocation = $this->contentDomainMapper->buildLocation(
            $this->persistenceHandler->locationHandler()->load($locationCreateStruct->parentLocationId)
        );

        $contentType = $content->getContentType();

        $locationCreateStruct->sortField = $locationCreateStruct->sortField
            ?? ($contentType->defaultSortField ?? Location::SORT_FIELD_NAME);
        $locationCreateStruct->sortOrder = $locationCreateStruct->sortOrder
            ?? ($contentType->defaultSortOrder ?? Location::SORT_ORDER_ASC);

        $contentInfo = $content->contentInfo;

        if (!$this->permissionResolver->canUser('content', 'manage_locations', $contentInfo, [$parentLocation])) {
            throw new UnauthorizedException('content', 'manage_locations', ['contentId' => $contentInfo->id]);
        }

        if (!$this->permissionResolver->canUser('content', 'create', $contentInfo, [$parentLocation])) {
            throw new UnauthorizedException('content', 'create', ['locationId' => $parentLocation->id]);
        }

        // Check if the parent is a sub location of one of the existing content locations (this also solves the
        // situation where parent location actually one of the content locations),
        // or if the content already has location below given location create struct parent
        $existingContentLocations = $this->loadLocations($contentInfo);
        if (!empty($existingContentLocations)) {
            foreach ($existingContentLocations as $existingContentLocation) {
                if (stripos($parentLocation->pathString, $existingContentLocation->pathString) !== false) {
                    throw new InvalidArgumentException(
                        '$locationCreateStruct',
                        'Specified parent is a descendant of one of the existing Locations of this content.'
                    );
                }
                if ($parentLocation->id == $existingContentLocation->parentLocationId) {
                    throw new InvalidArgumentException(
                        '$locationCreateStruct',
                        'Content is already below the specified parent.'
                    );
                }
            }
        }

        $spiLocationCreateStruct = $this->contentDomainMapper->buildSPILocationCreateStruct(
            $locationCreateStruct,
            $parentLocation,
            $contentInfo->mainLocationId ?? true,
            $contentInfo->id,
            $contentInfo->currentVersionNo,
            $contentInfo->isHidden
        );

        $this->repository->beginTransaction();
        try {
            $newLocation = $this->persistenceHandler->locationHandler()->create($spiLocationCreateStruct);
            $urlAliasNames = $this->nameSchemaService->resolveUrlAliasSchema($content);
            foreach ($urlAliasNames as $languageCode => $name) {
                $this->persistenceHandler->urlAliasHandler()->publishUrlAliasForLocation(
                    $newLocation->id,
                    $newLocation->parentId,
                    $name,
                    $languageCode,
                    $contentInfo->alwaysAvailable,
                    // @todo: this is legacy storage specific for updating ezcontentobject_tree.path_identification_string, to be removed
                    $languageCode === $contentInfo->mainLanguageCode
                );
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->contentDomainMapper->buildLocationWithContent($newLocation, $content);
    }

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
    public function updateLocation(APILocation $location, LocationUpdateStruct $locationUpdateStruct): APILocation
    {
        if (!$this->contentDomainMapper->isValidLocationPriority($locationUpdateStruct->priority)) {
            throw new InvalidArgumentValue('priority', $locationUpdateStruct->priority, 'LocationUpdateStruct');
        }

        if ($locationUpdateStruct->remoteId !== null && (!is_string($locationUpdateStruct->remoteId) || empty($locationUpdateStruct->remoteId))) {
            throw new InvalidArgumentValue('remoteId', $locationUpdateStruct->remoteId, 'LocationUpdateStruct');
        }

        if ($locationUpdateStruct->sortField !== null && !$this->contentDomainMapper->isValidLocationSortField($locationUpdateStruct->sortField)) {
            throw new InvalidArgumentValue('sortField', $locationUpdateStruct->sortField, 'LocationUpdateStruct');
        }

        if ($locationUpdateStruct->sortOrder !== null && !$this->contentDomainMapper->isValidLocationSortOrder($locationUpdateStruct->sortOrder)) {
            throw new InvalidArgumentValue('sortOrder', $locationUpdateStruct->sortOrder, 'LocationUpdateStruct');
        }

        $loadedLocation = $this->loadLocation($location->id);

        if ($locationUpdateStruct->remoteId !== null) {
            try {
                $existingLocation = $this->loadLocationByRemoteId($locationUpdateStruct->remoteId);
                if ($existingLocation !== null && $existingLocation->id !== $loadedLocation->id) {
                    throw new InvalidArgumentException('locationUpdateStruct', 'Location with the provided remote ID already exists');
                }
            } catch (APINotFoundException $e) {
            }
        }

        if (!$this->permissionResolver->canUser('content', 'edit', $loadedLocation->getContentInfo(), [$loadedLocation])) {
            throw new UnauthorizedException('content', 'edit', ['locationId' => $loadedLocation->id]);
        }

        $updateStruct = new UpdateStruct();
        $updateStruct->priority = $locationUpdateStruct->priority !== null ? $locationUpdateStruct->priority : $loadedLocation->priority;
        $updateStruct->remoteId = $locationUpdateStruct->remoteId !== null ? trim($locationUpdateStruct->remoteId) : $loadedLocation->remoteId;
        $updateStruct->sortField = $locationUpdateStruct->sortField !== null ? $locationUpdateStruct->sortField : $loadedLocation->sortField;
        $updateStruct->sortOrder = $locationUpdateStruct->sortOrder !== null ? $locationUpdateStruct->sortOrder : $loadedLocation->sortOrder;

        $this->repository->beginTransaction();
        try {
            $this->persistenceHandler->locationHandler()->update($updateStruct, $loadedLocation->id);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->loadLocation($loadedLocation->id);
    }

    /**
     * Swaps the contents held by $location1 and $location2.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to swap content
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2
     */
    public function swapLocation(APILocation $location1, APILocation $location2): void
    {
        $loadedLocation1 = $this->loadLocation($location1->id);
        $loadedLocation2 = $this->loadLocation($location2->id);

        if (!$this->permissionResolver->canUser('content', 'edit', $loadedLocation1->getContentInfo(), [$loadedLocation1])) {
            throw new UnauthorizedException('content', 'edit', ['locationId' => $loadedLocation1->id]);
        }
        if (!$this->permissionResolver->canUser('content', 'edit', $loadedLocation2->getContentInfo(), [$loadedLocation2])) {
            throw new UnauthorizedException('content', 'edit', ['locationId' => $loadedLocation2->id]);
        }

        $this->repository->beginTransaction();
        try {
            $this->persistenceHandler->locationHandler()->swap($loadedLocation1->id, $loadedLocation2->id);
            $this->persistenceHandler->urlAliasHandler()->locationSwapped(
                $location1->id,
                $location1->parentLocationId,
                $location2->id,
                $location2->parentLocationId
            );
            $this->persistenceHandler->bookmarkHandler()->locationSwapped($loadedLocation1->id, $loadedLocation2->id);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * Hides the $location and marks invisible all descendants of $location.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to hide this location
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location $location, with updated hidden value
     */
    public function hideLocation(APILocation $location): APILocation
    {
        if (!$this->permissionResolver->canUser('content', 'hide', $location->getContentInfo(), [$location])) {
            throw new UnauthorizedException('content', 'hide', ['locationId' => $location->id]);
        }

        $this->repository->beginTransaction();
        try {
            $this->persistenceHandler->locationHandler()->hide($location->id);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->loadLocation($location->id);
    }

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
    public function unhideLocation(APILocation $location): APILocation
    {
        if (!$this->permissionResolver->canUser('content', 'hide', $location->getContentInfo(), [$location])) {
            throw new UnauthorizedException('content', 'hide', ['locationId' => $location->id]);
        }

        $this->repository->beginTransaction();
        try {
            $this->persistenceHandler->locationHandler()->unHide($location->id);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->loadLocation($location->id);
    }

    /**
     * {@inheritdoc}
     */
    public function moveSubtree(APILocation $location, APILocation $newParentLocation): void
    {
        $location = $this->loadLocation($location->id);
        $newParentLocation = $this->loadLocation($newParentLocation->id);

        if ($newParentLocation->id === $location->parentLocationId) {
            throw new InvalidArgumentException(
                '$newParentLocation',
                'new parent Location is the same as the current one'
            );
        }
        if (strpos($newParentLocation->pathString, $location->pathString) === 0) {
            throw new InvalidArgumentException(
                '$newParentLocation',
                'new parent Location is a descendant of the given $location'
            );
        }
        $contentTypeId = $newParentLocation->contentInfo->contentTypeId;
        if (!$this->contentTypeService->loadContentType($contentTypeId)->isContainer) {
            throw new InvalidArgumentException(
                '$newParentLocation',
                'Cannot move Location to a parent that is not a container'
            );
        }

        // check create permission on target location
        if (!$this->permissionResolver->canUser('content', 'create', $location->getContentInfo(), [$newParentLocation])) {
            throw new UnauthorizedException('content', 'create', ['locationId' => $newParentLocation->id]);
        }

        // Check read access to whole source subtree
        $contentReadCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'read');
        if ($contentReadCriterion === false) {
            throw new UnauthorizedException('content', 'read');
        } elseif ($contentReadCriterion !== true) {
            // Query if there are any content in subtree current user don't have access to
            $query = new Query(
                [
                    'limit' => 0,
                    'filter' => new CriterionLogicalAnd(
                        [
                            new CriterionSubtree($location->pathString),
                            new CriterionLogicalNot($contentReadCriterion),
                        ]
                    ),
                ]
            );
            $result = $this->repository->getSearchService()->findContent($query, [], false);
            if ($result->totalCount > 0) {
                throw new UnauthorizedException('content', 'read');
            }
        }

        $this->repository->beginTransaction();
        try {
            $this->persistenceHandler->locationHandler()->move($location->id, $newParentLocation->id);

            $content = $this->repository->getContentService()->loadContent($location->contentId);
            $urlAliasNames = $this->nameSchemaService->resolveUrlAliasSchema($content);
            foreach ($urlAliasNames as $languageCode => $name) {
                $this->persistenceHandler->urlAliasHandler()->publishUrlAliasForLocation(
                    $location->id,
                    $newParentLocation->id,
                    $name,
                    $languageCode,
                    $content->contentInfo->alwaysAvailable
                );
            }

            $this->persistenceHandler->urlAliasHandler()->locationMoved(
                $location->id,
                $location->parentLocationId,
                $newParentLocation->id
            );

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * Deletes $location and all its descendants.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user is not allowed to delete this location or a descendant
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    public function deleteLocation(APILocation $location): void
    {
        $location = $this->loadLocation($location->id);
        $contentInfo = $location->contentInfo;
        $versionInfo = $this->persistenceHandler->contentHandler()->loadVersionInfo(
            $contentInfo->id,
            $contentInfo->currentVersionNo
        );
        $translations = $versionInfo->languageCodes;
        $target = (new Target\Version())->deleteTranslations($translations);

        if (!$this->permissionResolver->canUser('content', 'manage_locations', $location->getContentInfo())) {
            throw new UnauthorizedException('content', 'manage_locations', ['locationId' => $location->id]);
        }
        if (!$this->permissionResolver->canUser('content', 'remove', $location->getContentInfo(), [$location, $target])) {
            throw new UnauthorizedException('content', 'remove', ['locationId' => $location->id]);
        }

        // Check remove access to descendants
        $contentReadCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'remove');
        if ($contentReadCriterion === false) {
            throw new UnauthorizedException('content', 'remove');
        } elseif ($contentReadCriterion !== true) {
            // Query if there are any content in subtree current user don't have access to
            $query = new Query(
                [
                    'limit' => 0,
                    'filter' => new CriterionLogicalAnd(
                        [
                            new CriterionSubtree($location->pathString),
                            new CriterionLogicalNot($contentReadCriterion),
                        ]
                    ),
                ]
            );
            $result = $this->repository->getSearchService()->findContent($query, [], false);
            if ($result->totalCount > 0) {
                throw new UnauthorizedException('content', 'remove');
            }
        }

        $this->repository->beginTransaction();
        try {
            $this->persistenceHandler->locationHandler()->removeSubtree($location->id);
            $this->persistenceHandler->urlAliasHandler()->locationDeleted($location->id);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * Instantiates a new location create class.
     *
     * @param mixed $parentLocationId the parent under which the new location should be created
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|null $contentType
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct
     */
    public function newLocationCreateStruct($parentLocationId, ContentType $contentType = null): LocationCreateStruct
    {
        $properties = [
            'parentLocationId' => $parentLocationId,
        ];
        if ($contentType) {
            $properties['sortField'] = $contentType->defaultSortField;
            $properties['sortOrder'] = $contentType->defaultSortOrder;
        }

        return new LocationCreateStruct($properties);
    }

    /**
     * Instantiates a new location update class.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct
     */
    public function newLocationUpdateStruct(): LocationUpdateStruct
    {
        return new LocationUpdateStruct();
    }

    /**
     * Get the total number of all existing Locations. Can be combined with loadAllLocations.
     *
     * @see loadAllLocations
     *
     * @return int Total number of Locations
     */
    public function getAllLocationsCount(): int
    {
        return $this->persistenceHandler->locationHandler()->countAllLocations();
    }

    /**
     * Bulk-load all existing Locations, constrained by $limit and $offset to paginate results.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function loadAllLocations(int $offset = 0, int $limit = 25): array
    {
        $spiLocations = $this->persistenceHandler->locationHandler()->loadAllLocations(
            $offset,
            $limit
        );
        $contentIds = array_unique(
            array_map(
                static function (SPILocation $spiLocation) {
                    return $spiLocation->contentId;
                },
                $spiLocations
            )
        );

        $permissionResolver = $this->repository->getPermissionResolver();
        $spiContentInfoList = $this->persistenceHandler->contentHandler()->loadContentInfoList(
            $contentIds
        );
        $contentList = $this->contentDomainMapper->buildContentProxyList(
            $spiContentInfoList,
            Language::ALL,
            false
        );
        $locations = [];
        foreach ($spiLocations as $spiLocation) {
            if (!isset($spiContentInfoList[$spiLocation->contentId], $contentList[$spiLocation->contentId])) {
                $this->logger->warning(
                    sprintf(
                        'Location %d has missing content %d',
                        $spiLocation->id,
                        $spiLocation->contentId
                    )
                );
                continue;
            }

            $location = $this->contentDomainMapper->buildLocationWithContent(
                $spiLocation,
                $contentList[$spiLocation->contentId],
                $spiContentInfoList[$spiLocation->contentId]
            );

            $contentInfo = $location->getContentInfo();
            if (!$permissionResolver->canUser('content', 'read', $contentInfo, [$location])) {
                continue;
            }
            $locations[] = $location;
        }

        return $locations;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function find(Filter $filter, ?array $languages = null): LocationList
    {
        $filter = clone $filter;
        if (!empty($languages)) {
            $filter->andWithCriterion(new LanguageCode($languages));
        }

        $permissionCriterion = $this->permissionCriterionResolver->getQueryPermissionsCriterion();
        if ($permissionCriterion instanceof Criterion\MatchNone) {
            return new LocationList();
        }

        if (!$permissionCriterion instanceof Criterion\MatchAll) {
            if (!$permissionCriterion instanceof FilteringCriterion) {
                return new LocationList();
            }
            $filter->andWithCriterion($permissionCriterion);
        }

        $locations = [];
        foreach ($this->locationFilteringHandler->find($filter) as $locationWithContentInfo) {
            $spiContentInfo = $locationWithContentInfo->getContentInfo();
            $locations[] = $this->contentDomainMapper->buildLocationWithContent(
                $locationWithContentInfo->getLocation(),
                $this->contentDomainMapper->buildContentProxy($spiContentInfo),
                $spiContentInfo,
            );
        }

        return new LocationList(
            [
                'totalCount' => count($locations),
                'locations' => $locations,
            ]
        );
    }
}

class_alias(LocationService::class, 'eZ\Publish\Core\Repository\LocationService');
