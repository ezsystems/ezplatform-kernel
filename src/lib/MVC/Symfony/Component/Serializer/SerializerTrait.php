<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Component\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

trait SerializerTrait
{
    public function getSerializer(): SerializerInterface
    {
        return new Serializer(
            [
                new CompoundMatcherNormalizer(),
                new HostElementNormalizer(),
                new MapNormalizer(),
                new URITextNormalizer(),
                new HostTextNormalizer(),
                new RegexURINormalizer(),
                new RegexHostNormalizer(),
                new RegexNormalizer(),
                new URIElementNormalizer(),
                new SimplifiedRequestNormalizer(),
                new JsonSerializableNormalizer(),
                new PropertyNormalizer(),
            ],
            [new JsonEncoder()]
        );
    }
}

class_alias(SerializerTrait::class, 'eZ\Publish\Core\MVC\Symfony\Component\Serializer\SerializerTrait');
