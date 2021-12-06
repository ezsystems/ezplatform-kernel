<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;

class ContentType extends MultipleValued
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
        return isset($this->values[$location->getContentInfo()->contentTypeId]);
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
        return isset($this->values[$contentInfo->contentTypeId]);
    }

    public function match(View $view)
    {
        if (!$view instanceof ContentValueView) {
            return false;
        }

        return isset($this->values[$view->getContent()->contentInfo->contentTypeId]);
    }
}

class_alias(ContentType::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id\ContentType');
