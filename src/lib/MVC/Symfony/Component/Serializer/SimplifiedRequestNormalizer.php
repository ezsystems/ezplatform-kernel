<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

final class SimplifiedRequestNormalizer extends PropertyNormalizer
{
    /**
     * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize
     *
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'scheme' => $object->scheme,
            'host' => $object->host,
            'port' => $object->port,
            'pathinfo' => $object->pathinfo,
            'queryParams' => $object->queryParams,
            'languages' => $object->languages,
            'headers' => [],
        ];
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof SimplifiedRequest;
    }
}

class_alias(SimplifiedRequestNormalizer::class, 'eZ\Publish\Core\MVC\Symfony\Component\Serializer\SimplifiedRequestNormalizer');
