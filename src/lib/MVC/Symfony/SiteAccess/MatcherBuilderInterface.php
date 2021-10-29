<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;

interface MatcherBuilderInterface
{
    /**
     * Builds siteaccess matcher.
     *
     * @param string $matcherIdentifier "Identifier" of the matcher to build (i.e. its FQ class name).
     * @param mixed $matchingConfiguration Configuration to pass to the matcher. Can be anything the matcher supports.
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request The request to match against.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher
     *
     * @throws \RuntimeException
     */
    public function buildMatcher($matcherIdentifier, $matchingConfiguration, SimplifiedRequest $request);
}

class_alias(MatcherBuilderInterface::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\MatcherBuilderInterface');
