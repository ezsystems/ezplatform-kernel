<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\URIElement;

final class URIElementNormalizer extends AbstractPropertyWhitelistNormalizer
{
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof URIElement;
    }

    /**
     * @see \Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\URIElement::__sleep
     */
    protected function getAllowedProperties(): array
    {
        return ['elementNumber', 'uriElements'];
    }
}

class_alias(URIElementNormalizer::class, 'eZ\Publish\Core\MVC\Symfony\Component\Serializer\URIElementNormalizer');
