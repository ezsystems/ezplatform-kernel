<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Matcher\ContentBased;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Ibexa\Core\MVC\Symfony\View\View;

class UrlAlias extends MultipleValued
{
    /**
     * Checks if a Location object matches.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function matchLocation(Location $location)
    {
        $urlAliasService = $this->repository->getURLAliasService();
        $locationUrls = array_merge(
            $urlAliasService->listLocationAliases($location),
            $urlAliasService->listLocationAliases($location, false)
        );

        foreach ($this->values as $pattern => $val) {
            foreach ($locationUrls as $urlAlias) {
                if (strpos((string)$urlAlias->path, "/$pattern") === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Not supported since UrlAlias is meaningful for location objects only.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function matchContentInfo(ContentInfo $contentInfo)
    {
        throw new \RuntimeException('matchContentInfo() is not supported by the UrlAlias matcher');
    }

    public function setMatchingConfig($matchingConfig)
    {
        if (!is_array($matchingConfig)) {
            $matchingConfig = [$matchingConfig];
        }

        array_walk(
            $matchingConfig,
            static function (&$item) {
                $item = trim($item, '/ ');
            }
        );

        parent::setMatchingConfig($matchingConfig);
    }

    public function match(View $view)
    {
        if (!$view instanceof LocationValueView) {
            return false;
        }

        return $this->matchLocation($view->getLocation());
    }
}

class_alias(UrlAlias::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\UrlAlias');
