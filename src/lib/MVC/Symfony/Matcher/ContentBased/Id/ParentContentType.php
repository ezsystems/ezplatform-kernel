<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Ibexa\Core\MVC\Symfony\View\View;

class ParentContentType extends MultipleValued
{
    /**
     * Checks if a Location object matches.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function matchLocation(APILocation $location)
    {
        $parent = $this->repository->sudo(
            static function (Repository $repository) use ($location) {
                return $repository->getLocationService()->loadLocation($location->parentLocationId);
            }
        );

        return isset($this->values[$parent->getContentInfo()->contentTypeId]);
    }

    /**
     * Checks if a ContentInfo object matches.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return bool
     */
    public function matchContentInfo(ContentInfo $contentInfo)
    {
        $location = $this->repository->sudo(
            static function (Repository $repository) use ($contentInfo) {
                return $repository->getLocationService()->loadLocation($contentInfo->mainLocationId);
            }
        );

        return $this->matchLocation($location);
    }

    public function match(View $view)
    {
        if (!$view instanceof LocationValueView) {
            return false;
        }
        $parent = $this->loadParentLocation(
            $view->getLocation()->parentLocationId
        );

        return isset($this->values[$parent->getContentInfo()->contentTypeId]);
    }

    /**
     * @return Location
     */
    private function loadParentLocation($locationId)
    {
        return $this->repository->sudo(
            static function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );
    }
}

class_alias(ParentContentType::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id\ParentContentType');
