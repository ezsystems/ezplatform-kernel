<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\HostElement;

final class HostElementNormalizer extends AbstractPropertyWhitelistNormalizer
{
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof HostElement;
    }

    /**
     * @see \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\HostElement::__sleep
     */
    protected function getAllowedProperties(): array
    {
        return ['elementNumber', 'hostElements'];
    }
}

class_alias(HostElementNormalizer::class, 'eZ\Publish\Core\MVC\Symfony\Component\Serializer\HostElementNormalizer');
