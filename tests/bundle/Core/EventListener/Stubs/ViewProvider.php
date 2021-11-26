<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener\Stubs;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewProvider as ViewProviderInterface;

/**
 * Stub class for SiteAccessAware ViewProvider.
 */
class ViewProvider implements ViewProviderInterface, SiteAccessAware
{
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\View\View
     */
    public function getView(View $view)
    {
    }
}

class_alias(ViewProvider::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\Stubs\ViewProvider');
