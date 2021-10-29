<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;

interface SiteAccessRouterInterface
{
    /**
     * Performs SiteAccess matching given the $request.
     *
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     *
     * @throws \Ibexa\Core\MVC\Exception\InvalidSiteAccessException
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function match(SimplifiedRequest $request);

    /**
     * Matches a SiteAccess by name.
     * Returns corresponding SiteAccess object, according to configuration, with corresponding matcher.
     * If no matcher can be found (e.g. non versatile), matcher property will be "default".
     *
     * @param string $siteAccessName
     *
     * @throws \InvalidArgumentException If $siteAccessName is invalid (i.e. not present in configured list).
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function matchByName($siteAccessName);
}

class_alias(SiteAccessRouterInterface::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface');
