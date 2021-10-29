<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Regex;

final class RegexNormalizer extends AbstractPropertyWhitelistNormalizer
{
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Regex;
    }

    /**
     * @see \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Regex::__sleep
     */
    protected function getAllowedProperties(): array
    {
        return ['regex', 'itemNumber', 'matchedSiteAccess'];
    }
}

class_alias(RegexNormalizer::class, 'eZ\Publish\Core\MVC\Symfony\Component\Serializer\RegexNormalizer');
