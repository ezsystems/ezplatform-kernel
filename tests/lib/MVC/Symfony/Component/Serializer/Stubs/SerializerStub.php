<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerStub implements SerializerInterface, NormalizerInterface
{
    public function serialize($data, $format, array $context = [])
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function deserialize($data, $type, $format, array $context = [])
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (is_array($object)) {
            $result = [];
            foreach ($object as $key => $value) {
                $result[$key] = $this->normalize($value, $format, $context);
            }

            return $result;
        }

        if ($object instanceof MatcherStub) {
            return [
                'data' => $object->getData(),
            ];
        }

        return $object;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return true;
    }
}

class_alias(SerializerStub::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\Stubs\SerializerStub');
