<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\MatcherBuilderInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\VersatileMatcher;

interface CompoundInterface extends VersatileMatcher
{
    /**
     * Injects the matcher builder, to allow the Compound matcher to properly build the underlying matchers.
     *
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\MatcherBuilderInterface $matcherBuilder
     */
    public function setMatcherBuilder(MatcherBuilderInterface $matcherBuilder);

    /**
     * Returns all used sub-matchers.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher[]
     */
    public function getSubMatchers();

    /**
     * Replaces sub-matchers.
     *
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher[] $subMatchers
     */
    public function setSubMatchers(array $subMatchers);
}

class_alias(CompoundInterface::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Matcher\CompoundInterface');
