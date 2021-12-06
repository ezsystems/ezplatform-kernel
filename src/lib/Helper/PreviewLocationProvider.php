<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Helper;

use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as PersistenceLocationHandler;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\Repository\Values\Content\Location;

/**
 * Provides location(s) for a content. Handles unpublished content that does not have an actual location yet.
 */
class PreviewLocationProvider
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler */
    private $locationHandler;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Persistence\Content\Location\Handler $locationHandler
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        PersistenceLocationHandler $locationHandler
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->locationHandler = $locationHandler;
    }

    /**
     * Loads the main location for $contentId.
     *
     * If the content does not have a location (yet), but has a Location draft, it is returned instead.
     * Location drafts do not have an id (it is set to null), and can be tested using the isDraft() method.
     *
     * If the content doesn't have a location nor a location draft, null is returned.
     *
     * @deprecated Since 7.5.4, rather use loadMainLocationByContent.
     * @see loadMainLocationByContent
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @param mixed $contentId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location|null
     */
    public function loadMainLocation($contentId)
    {
        return $this->loadMainLocationByContent(
            $this->contentService->loadContent($contentId)
        );
    }

    /**
     * Loads the main location for $content.
     *
     * If the content does not have a location (yet), but has a Location draft, it is returned instead.
     * Location drafts do not have an id (it is set to null), and can be tested using the isDraft() method.
     *
     * If the content doesn't have a location nor a location draft, null is returned.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location|null
     */
    public function loadMainLocationByContent(APIContent $content): ?APILocation
    {
        $location = null;
        $contentInfo = $content
            ->getVersionInfo()
            ->getContentInfo();

        // mainLocationId already exists, content has been published at least once.
        if ($contentInfo->mainLocationId) {
            $location = $this->locationService->loadLocation($contentInfo->mainLocationId);
        } elseif (!$contentInfo->published) {
            // New Content, never published, create a virtual location object.
            // In cases content is missing locations this will return empty array
            $parentLocations = $this->locationHandler->loadParentLocationsForDraftContent($contentInfo->id);
            if (empty($parentLocations)) {
                return null;
            }

            // NOTE: Once Repository adds support for draft locations (and draft  location ops), then this can be removed
            $location = new Location(
                [
                    'content' => $content,
                    'contentInfo' => $contentInfo,
                    'status' => Location::STATUS_DRAFT,
                    'parentLocationId' => $parentLocations[0]->id,
                    'depth' => $parentLocations[0]->depth + 1,
                    'pathString' => $parentLocations[0]->pathString . 'x/',
                ]
            );
        }

        return $location;
    }
}

class_alias(PreviewLocationProvider::class, 'eZ\Publish\Core\Helper\PreviewLocationProvider');
