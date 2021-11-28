<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\RegexURINormalizer;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Regex\URI;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\SerializerStub;
use Ibexa\Tests\Core\Search\TestCase;

final class RegexURINormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new RegexURINormalizer();
        $normalizer->setSerializer(new SerializerStub());

        $matcher = new URI([
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
        $normalizer = new RegexURINormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->createMock(URI::class)));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}

class_alias(RegexURINormalizerTest::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\RegexURINormalizerTest');
