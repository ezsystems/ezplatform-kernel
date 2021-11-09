<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Routing;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest::fromUrl
 */
class SimplifiedRequestTest extends TestCase
{
    /**
     * @param string $url
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $expectedRequest
     *
     * @dataProvider fromUrlProvider
     */
    public function testFromUrl($url, $expectedRequest)
    {
        self::assertEquals(
            $expectedRequest,
            SimplifiedRequest::fromUrl($url)
        );
    }

    public function fromUrlProvider()
    {
        return [
            [
                'http://www.example.com/foo/bar',
                new SimplifiedRequest(
                    [
                        'scheme' => 'http',
                        'host' => 'www.example.com',
                        'pathinfo' => '/foo/bar',
                    ]
                ),
            ],
            [
                'https://www.example.com/',
                new SimplifiedRequest(
                    [
                        'scheme' => 'https',
                        'host' => 'www.example.com',
                        'pathinfo' => '/',
                    ]
                ),
            ],
            [
                'http://www.example.com/foo?param=value&this=that',
                new SimplifiedRequest(
                    [
                        'scheme' => 'http',
                        'host' => 'www.example.com',
                        'pathinfo' => '/foo',
                        'queryParams' => ['param' => 'value', 'this' => 'that'],
                    ]
                ),
            ],
        ];
    }
}

class_alias(SimplifiedRequestTest::class, 'eZ\Publish\Core\MVC\Symfony\Routing\Tests\SimplifiedRequestTest');
