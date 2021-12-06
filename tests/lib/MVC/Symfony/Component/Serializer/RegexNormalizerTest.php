<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\RegexNormalizer;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Regex as RegexMatcher;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\RegexMatcher as RegexMatcherStub;
use PHPUnit\Framework\TestCase;

final class RegexNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new RegexNormalizer();
        $matcher = new RegexMatcherStub('/^Foo(.*)/(.*)/', 2);

        $this->assertEquals(
            [
                'regex' => '/^Foo(.*)/(.*)/',
                'itemNumber' => 2,
                'matchedSiteAccess' => null,
            ],
            $normalizer->normalize($matcher)
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new RegexNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->createMock(RegexMatcher::class)));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}

class_alias(RegexNormalizerTest::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\RegexNormalizerTest');
