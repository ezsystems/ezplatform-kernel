<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\SiteAccess;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher as BaseMatcher;

/**
 * Interface for service based siteaccess matchers.
 */
interface Matcher extends BaseMatcher
{
    /**
     * Registers the matching configuration associated with the matcher.
     *
     * @param mixed $matchingConfiguration
     */
    public function setMatchingConfiguration($matchingConfiguration);
}

class_alias(Matcher::class, 'eZ\Bundle\EzPublishCoreBundle\SiteAccess\Matcher');
