<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\View\Provider;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\Core\MVC\Symfony\View\Provider\Configured as BaseConfigured;

class Configured extends BaseConfigured implements SiteAccessAware
{
    /**
     * Changes SiteAccess.
     *
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $siteAccess
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        if ($this->matcherFactory instanceof SiteAccessAware) {
            $this->matcherFactory->setSiteAccess($siteAccess);
        }
    }
}

class_alias(Configured::class, 'eZ\Bundle\EzPublishCoreBundle\View\Provider\Configured');
