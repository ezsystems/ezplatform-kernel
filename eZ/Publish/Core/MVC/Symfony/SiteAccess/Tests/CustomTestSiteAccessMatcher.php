<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\SiteAccess\Tests;

use eZ\Bundle\EzPublishCoreBundle\SiteAccess\Matcher;
use eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest;

/**
 * @internal For test purposes only!
 */
final class CustomTestSiteAccessMatcher implements Matcher
{
    /** @var SimplifiedRequest */
    private $request;

    /** @var string */
    private $pathinfo;

    /** @var string */
    private $host;

    public function setRequest(SimplifiedRequest $request)
    {
        $this->request = $request;
    }

    public function match()
    {
        $this->pathinfo = $this->request->pathinfo;
        $this->host = $this->request->host;

        return false;
    }

    public function getName(): string
    {
        return 'custom_test_matcher';
    }

    public function setMatchingConfiguration($matchingConfiguration): void
    {
    }
}
