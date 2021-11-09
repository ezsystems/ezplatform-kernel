<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;

/**
 * Interface for SiteAccess matchers.
 */
interface Matcher
{
    /**
     * Injects the request object to match against.
     *
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     */
    public function setRequest(SimplifiedRequest $request);

    /**
     * Returns matched Siteaccess or false if no siteaccess could be matched.
     *
     * @return string|false
     */
    public function match();

    /**
     * Returns the matcher's name.
     * This information will be stored in the SiteAccess object itself to quickly be able to identify the matcher type.
     *
     * @return string
     */
    public function getName();
}

class_alias(Matcher::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Matcher');
