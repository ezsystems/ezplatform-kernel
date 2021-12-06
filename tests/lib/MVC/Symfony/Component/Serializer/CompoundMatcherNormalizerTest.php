<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\CompoundMatcherNormalizer;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Compound;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\CompoundStub;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\MatcherStub;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\SerializerStub;
use PHPUnit\Framework\TestCase;

final class CompoundMatcherNormalizerTest extends TestCase
{
    public function testNormalization(): void
    {
        $matcher = new CompoundStub([]);
        $matcher->setSubMatchers([
            'foo' => new MatcherStub('foo'),
            'bar' => new MatcherStub('bar'),
            'baz' => new MatcherStub('baz'),
        ]);

        $normalizer = new CompoundMatcherNormalizer();
        $normalizer->setSerializer(new SerializerStub());

        $this->assertEquals(
            [
                'subMatchers' => [
                    'foo' => ['data' => 'foo'],
                    'bar' => ['data' => 'bar'],
                    'baz' => ['data' => 'baz'],
                ],
                'config' => [],
                'matchersMap' => [],
            ],
            $normalizer->normalize($matcher)
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new CompoundMatcherNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->createMock(Compound::class)));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}

class_alias(CompoundMatcherNormalizerTest::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\CompoundMatcherNormalizerTest');
