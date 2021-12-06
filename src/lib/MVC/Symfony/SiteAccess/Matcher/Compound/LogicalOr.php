<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Compound;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Compound;
use Ibexa\Core\MVC\Symfony\SiteAccess\VersatileMatcher;

/**
 * Siteaccess matcher that allows a combination of matchers, with a logical OR.
 */
class LogicalOr extends Compound
{
    public const NAME = 'logicalOr';

    public function match()
    {
        foreach ($this->config as $i => $rule) {
            foreach ($rule['matchers'] as $subMatcherClass => $matchingConfig) {
                if ($this->matchersMap[$i][$subMatcherClass]->match()) {
                    $this->subMatchers = $this->matchersMap[$i];

                    return $rule['match'];
                }
            }
        }

        return false;
    }

    public function reverseMatch($siteAccessName)
    {
        foreach ($this->config as $i => $rule) {
            if ($rule['match'] === $siteAccessName) {
                foreach ($this->matchersMap[$i] as $subMatcher) {
                    if (!$subMatcher instanceof VersatileMatcher) {
                        continue;
                    }

                    $reverseMatcher = $subMatcher->reverseMatch($siteAccessName);
                    if (!$reverseMatcher) {
                        continue;
                    }

                    $this->setSubMatchers([$subMatcher]);

                    return $this;
                }
            }
        }
    }
}

class_alias(LogicalOr::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Matcher\Compound\LogicalOr');
