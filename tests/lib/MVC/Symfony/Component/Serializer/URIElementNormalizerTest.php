<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\URIElementNormalizer;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\URIElement;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\SerializerStub;
use PHPUnit\Framework\TestCase;

final class URIElementNormalizerTest extends TestCase
{
    public function testNormalization(): void
    {
        $normalizer = new URIElementNormalizer();
        $normalizer->setSerializer(new SerializerStub());

        $matcher = new URIElement(2);
        // Set request and invoke match to initialize HostElement::$hostElements
        $matcher->setRequest(SimplifiedRequest::fromUrl('http://ezpublish.dev/foo/bar'));
        $matcher->match();

        $this->assertEquals(
            [
                'elementNumber' => 2,
                'uriElements' => ['foo', 'bar'],
            ],
            $normalizer->normalize($matcher)
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new URIElementNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->createMock(URIElement::class)));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}

class_alias(URIElementNormalizerTest::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\URIElementNormalizerTest');
