<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Identifier;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
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
        $parentContentType = $this->repository->sudo(
            static function (Repository $repository) use ($location) {
                $parent = $repository->getLocationService()->loadLocation($location->parentLocationId);

                return $repository
                    ->getContentTypeService()
                    ->loadContentType($parent->getContentInfo()->contentTypeId);
            }
        );

        return isset($this->values[$parentContentType->identifier]);
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
        if ($view instanceof LocationValueView) {
            return $this->matchLocation($view->getLocation());
        }

        if ($view instanceof ContentValueView) {
            return $this->matchContentInfo($view->getContent()->contentInfo);
        }

        return false;
    }
}

class_alias(ParentContentType::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Identifier\ParentContentType');
