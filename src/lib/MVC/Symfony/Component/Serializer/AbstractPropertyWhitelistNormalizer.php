<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Component\Serializer;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

abstract class AbstractPropertyWhitelistNormalizer extends PropertyNormalizer
{
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);
        foreach (array_keys($data) as $property) {
            if (!in_array($property, $this->getAllowedProperties())) {
                unset($data[$property]);
            }
        }

        return $data;
    }

    /**
     * @return string[]
     */
    abstract protected function getAllowedProperties(): array;
}

class_alias(AbstractPropertyWhitelistNormalizer::class, 'eZ\Publish\Core\MVC\Symfony\Component\Serializer\AbstractPropertyWhitelistNormalizer');
