<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\SiteAccess;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\MatcherBuilder as BaseMatcherBuilder;

/**
 * Siteaccess matcher builder based on services.
 */
final class MatcherBuilder extends BaseMatcherBuilder
{
    /** @var \Ibexa\Bundle\Core\SiteAccess\SiteAccessMatcherRegistryInterface */
    protected $siteAccessMatcherRegistry;

    public function __construct(SiteAccessMatcherRegistryInterface $siteAccessMatcherRegistry)
    {
        $this->siteAccessMatcherRegistry = $siteAccessMatcherRegistry;
    }

    /**
     * Builds siteaccess matcher.
     * If $matchingClass begins with "@", it will be considered as a service identifier.
     *
     * @param $matchingClass
     * @param $matchingConfiguration
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     *
     * @return \Ibexa\Bundle\Core\SiteAccess\Matcher
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function buildMatcher($matchingClass, $matchingConfiguration, SimplifiedRequest $request)
    {
        if (strpos($matchingClass, '@') === 0) {
            $matcher = $this->siteAccessMatcherRegistry->getMatcher(substr($matchingClass, 1));

            $matcher->setMatchingConfiguration($matchingConfiguration);
            $matcher->setRequest($request);

            return $matcher;
        }

        return parent::buildMatcher($matchingClass, $matchingConfiguration, $request);
    }
}

class_alias(MatcherBuilder::class, 'eZ\Bundle\EzPublishCoreBundle\SiteAccess\MatcherBuilder');
