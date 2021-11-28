<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener\Stubs;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewManagerInterface;

/**
 * Stub class for SiteAccessAware ViewManager.
 */
class ViewManager implements ViewManagerInterface, SiteAccessAware
{
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
    }

    public function renderContent(
        Content $content,
        $viewType = ViewManagerInterface::VIEW_TYPE_FULL,
        $parameters = []
    ) {
    }

    public function renderLocation(
        Location $location,
        $viewType = ViewManagerInterface::VIEW_TYPE_FULL,
        $parameters = []
    ) {
    }

    public function renderContentView(View $view, array $defaultParams = [])
    {
    }
}

class_alias(ViewManager::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\Stubs\ViewManager');
