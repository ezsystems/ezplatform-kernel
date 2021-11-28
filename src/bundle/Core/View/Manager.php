<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\View;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\Core\MVC\Symfony\View\Manager as BaseManager;

class Manager extends BaseManager implements SiteAccessAware
{
    /**
     * Changes SiteAccess.
     * Passed SiteAccess will be injected in all location/content/block view providers
     * to allow them to change their internal configuration based on this new SiteAccess.
     *
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $siteAccess
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        if ($this->logger) {
            $this->logger->debug('Changing SiteAccess in view providers');
        }

        $providers = array_merge(
            $this->getAllLocationViewProviders(),
            $this->getAllContentViewProviders()
        );
        foreach ($providers as $provider) {
            if ($provider instanceof SiteAccessAware) {
                $provider->setSiteAccess($siteAccess);
            }
        }
    }
}

class_alias(Manager::class, 'eZ\Bundle\EzPublishCoreBundle\View\Manager');
