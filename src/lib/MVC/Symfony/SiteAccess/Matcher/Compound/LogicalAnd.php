<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Compound;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Compound;
use Ibexa\Core\MVC\Symfony\SiteAccess\VersatileMatcher;

/**
 * Siteaccess matcher that allows a combination of matchers, with a logical AND.
 */
class LogicalAnd extends Compound implements VersatileMatcher
{
    public const NAME = 'logicalAnd';

    public function match()
    {
        foreach ($this->config as $i => $rule) {
            foreach ($rule['matchers'] as $subMatcherClass => $matchingConfig) {
                // If at least one sub matcher doesn't match, jump to the next rule set.
                if ($this->matchersMap[$i][$subMatcherClass]->match() === false) {
                    continue 2;
                }
            }

            $this->subMatchers = $this->matchersMap[$i];

            return $rule['match'];
        }

        return false;
    }

    public function reverseMatch($siteAccessName)
    {
        foreach ($this->config as $i => $rule) {
            if ($rule['match'] === $siteAccessName) {
                $subMatchers = [];
                foreach ($this->matchersMap[$i] as $subMatcher) {
                    if (!$subMatcher instanceof VersatileMatcher) {
                        return null;
                    }

                    $reverseMatcher = $subMatcher->reverseMatch($siteAccessName);
                    if (!$reverseMatcher) {
                        return null;
                    }

                    $subMatchers[] = $subMatcher;
                }

                $this->setSubMatchers($subMatchers);

                return $this;
            }
        }
    }
}

class_alias(LogicalAnd::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Matcher\Compound\LogicalAnd');
