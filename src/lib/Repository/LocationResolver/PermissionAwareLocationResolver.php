<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\LocationResolver;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\Base\Exceptions\NotFoundException as CoreNotFoundException;

/**
 * @internal For internal use by eZ Platform core packages
 */
final class PermissionAwareLocationResolver implements LocationResolver
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function resolveLocation(ContentInfo $contentInfo): Location
    {
        try {
            if (null === $contentInfo->mainLocationId) {
                throw new CoreNotFoundException('location', $contentInfo->mainLocationId);
            }

            $location = $this->locationService->loadLocation($contentInfo->mainLocationId);
        } catch (NotFoundException | UnauthorizedException $e) {
            // try different locations if main location is not accessible for the user
            $locations = $this->locationService->loadLocations($contentInfo);
            if (empty($locations) || null === $contentInfo->mainLocationId) {
                throw $e;
            }

            // foreach to keep forward compatibility with a type of returned loadLocations() result
            foreach ($locations as $location) {
                return $location;
            }
        }

        return $location;
    }
}

class_alias(PermissionAwareLocationResolver::class, 'eZ\Publish\Core\Repository\LocationResolver\PermissionAwareLocationResolver');
