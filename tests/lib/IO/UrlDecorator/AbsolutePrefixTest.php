<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\IO\UrlDecorator;

use Ibexa\Core\IO\IOConfigProvider;
use Ibexa\Core\IO\UrlDecorator;
use Ibexa\Core\IO\UrlDecorator\AbsolutePrefix;

/**
 * Test case for IO Service.
 */
class AbsolutePrefixTest extends PrefixTest
{
    protected function buildDecorator(string $prefix): UrlDecorator
    {
        $ioConfigResolverMock = $this->createMock(IOConfigProvider::class);
        $ioConfigResolverMock
            ->method('getUrlPrefix')
            ->willReturn($prefix);

        return new AbsolutePrefix($ioConfigResolverMock);
    }

    public function provideData(): array
    {
        return [
            [
                'images/file.png',
                'var/storage',
                '/var/storage/images/file.png',
            ],
            [
                'images/file.png',
                'var/storage/',
                '/var/storage/images/file.png',
            ],
            [
                'images/file.png',
                'http://static.example.com',
                'http://static.example.com/images/file.png',
            ],
            [
                'images/file.png',
                'http://static.example.com/',
                'http://static.example.com/images/file.png',
            ],
            [
                'images/file.png',
                '//static.example.com',
                '//static.example.com/images/file.png',
            ],
            [
                'images/file.png',
                '//static.example.com/',
                '//static.example.com/images/file.png',
            ],
        ];
    }
}

class_alias(AbsolutePrefixTest::class, 'eZ\Publish\Core\IO\Tests\UrlDecorator\AbsolutePrefixTest');
