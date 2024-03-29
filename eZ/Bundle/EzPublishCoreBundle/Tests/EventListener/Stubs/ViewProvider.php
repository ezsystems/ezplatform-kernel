<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\Stubs;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\MVC\Symfony\View\ViewProvider as ViewProviderInterface;

/**
 * Stub class for SiteAccessAware ViewProvider.
 */
class ViewProvider implements ViewProviderInterface, SiteAccessAware
{
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
    }

    /**
     * @return \eZ\Publish\Core\MVC\Symfony\View\View
     */
    public function getView(View $view)
    {
    }
}
