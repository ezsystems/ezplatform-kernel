<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\HostElementNormalizer;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\HostElement;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\SerializerStub;
use PHPUnit\Framework\TestCase;

final class HostElementNormalizerTest extends TestCase
{
    public function testNormalization(): void
    {
        $normalizer = new HostElementNormalizer();
        $normalizer->setSerializer(new SerializerStub());

        $matcher = new HostElement(2);
        // Set request and invoke match to initialize HostElement::$hostElements
        $matcher->setRequest(SimplifiedRequest::fromUrl('http://ibexa.dev/foo/bar'));
        $matcher->match();

        $this->assertEquals(
            [
                'elementNumber' => 2,
                'hostElements' => [
                    'ibexa',
                    'dev',
                ],
            ],
            $normalizer->normalize($matcher)
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new HostElementNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->createMock(HostElement::class)));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}

class_alias(HostElementNormalizerTest::class, 'eZ\Publish\Core\MVC\Symfony\Component\Tests\Serializer\HostElementNormalizerTest');
