<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;

/**
 * Siteaccess matcher builder, based on class names.
 */
class MatcherBuilder implements MatcherBuilderInterface
{
    /**
     * Builds siteaccess matcher.
     * In the siteaccess configuration, if the matcher class begins with a "\" (FQ class name), it will be used as is, passing the matching configuration in the constructor.
     * Otherwise, given matching class will be relative to {@see \Ibexa\Core\MVC\Symfony\SiteAccess} namespace.
     *
     * @param string $matcherIdentifier "Identifier" of the matcher to build (i.e. its FQ class name).
     * @param mixed $matchingConfiguration Configuration to pass to the matcher. Can be anything the matcher supports.
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request The request to match against.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher
     */
    public function buildMatcher($matcherIdentifier, $matchingConfiguration, SimplifiedRequest $request)
    {
        // If class begins with a '\' it means it's a FQ class name,
        // otherwise it is relative to this namespace.
        if ($matcherIdentifier[0] !== '\\') {
            $matcherIdentifier = __NAMESPACE__ . "\\Matcher\\$matcherIdentifier";
        }

        /** @var $matcher \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher */
        $matcher = new $matcherIdentifier($matchingConfiguration);
        $matcher->setRequest($request);

        return $matcher;
    }
}

class_alias(MatcherBuilder::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\MatcherBuilder');
