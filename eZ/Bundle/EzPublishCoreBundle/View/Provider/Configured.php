<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\View\Provider;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use eZ\Publish\Core\MVC\Symfony\View\Provider\Configured as BaseConfigured;

class Configured extends BaseConfigured implements SiteAccessAware
{
    /**
     * Changes SiteAccess.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteAccess
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        if ($this->matcherFactory instanceof SiteAccessAware) {
            $this->matcherFactory->setSiteAccess($siteAccess);
        }
    }
}
