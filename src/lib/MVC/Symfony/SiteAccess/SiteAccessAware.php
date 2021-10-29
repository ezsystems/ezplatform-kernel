<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess;

use Ibexa\Core\MVC\Symfony\SiteAccess;

/**
 * Interface for SiteAccess aware services.
 */
interface SiteAccessAware
{
    public function setSiteAccess(SiteAccess $siteAccess = null);
}

class_alias(SiteAccessAware::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware');
