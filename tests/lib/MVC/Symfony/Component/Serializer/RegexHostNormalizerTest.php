<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\RegexHostNormalizer;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Regex\Host;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\SerializerStub;
use Ibexa\Tests\Core\Search\TestCase;

final class RegexHostNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new RegexHostNormalizer();
        $normalizer->setSerializer(new SerializerStub());

        $matcher = new Host([
            'regex' => '/^Foo(.*)/(.*)/',
            'itemNumber' => 2,
        ]);

        $this->assertEquals(
            [
                'siteAccessesConfiguration' => [
                    'regex' => '/^Foo(.*)/(.*)/',
                    'itemNumber' => 2,
                ],
            ],
            $normalizer->normalize($matcher)
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new RegexHostNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->createMock(Host::class)));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}

class_alias(RegexHostNormalizerTest::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\RegexHostNormalizerTest');
