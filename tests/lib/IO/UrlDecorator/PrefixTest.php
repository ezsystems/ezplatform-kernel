<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\IO\UrlDecorator;

use Ibexa\Core\IO\IOConfigProvider;
use Ibexa\Core\IO\UrlDecorator;
use Ibexa\Core\IO\UrlDecorator\Prefix;
use PHPUnit\Framework\TestCase;

class PrefixTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testDecorate($url, $prefix, $decoratedUrl)
    {
        $decorator = $this->buildDecorator($prefix);

        self::assertEquals(
            $decoratedUrl,
            $decorator->decorate($url)
        );
    }

    /**
     * @dataProvider provideData
     */
    public function testUndecorate($url, $prefix, $decoratedUrl)
    {
        $decorator = $this->buildDecorator($prefix);

        self::assertEquals(
            $url,
            $decorator->undecorate($decoratedUrl)
        );
    }

    protected function buildDecorator(string $prefix): UrlDecorator
    {
        $ioConfigResolverMock = $this->createMock(IOConfigProvider::class);
        $ioConfigResolverMock
            ->method('getLegacyUrlPrefix')
            ->willReturn($prefix);

        return new Prefix($ioConfigResolverMock);
    }

    public function provideData()
    {
        return [
            [
                'images/file.png',
                'var/storage',
                'var/storage/images/file.png',
            ],
            [
                'images/file.png',
                'var/storage/',
                'var/storage/images/file.png',
            ],
            [
                'images/file.png',
                'http://static.example.com',
                'http://static.example.com/images/file.png',
            ],
        ];
    }
}

class_alias(PrefixTest::class, 'eZ\Publish\Core\IO\Tests\UrlDecorator\PrefixTest');
