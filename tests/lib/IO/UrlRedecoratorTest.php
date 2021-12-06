<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\IO;

use Ibexa\Core\IO\UrlDecorator;
use Ibexa\Core\IO\UrlRedecorator;
use PHPUnit\Framework\TestCase;

class UrlRedecoratorTest extends TestCase
{
    /** @var \Ibexa\Core\IO\UrlRedecorator|\PHPUnit\Framework\MockObject\MockObject */
    private $redecorator;

    /** @var \Ibexa\Core\IO\UrlDecorator|\PHPUnit\Framework\MockObject\MockObject */
    private $sourceDecoratorMock;

    /** @var \Ibexa\Core\IO\UrlDecorator|\PHPUnit\Framework\MockObject\MockObject */
    private $targetDecoratorMock;

    protected function setUp(): void
    {
        $this->redecorator = new UrlRedecorator(
            $this->sourceDecoratorMock = $this->createMock(UrlDecorator::class),
            $this->targetDecoratorMock = $this->createMock(UrlDecorator::class)
        );
    }

    public function testRedecorateFromSource()
    {
        $this->sourceDecoratorMock
            ->expects($this->once())
            ->method('undecorate')
            ->with('http://static.example.com/images/file.png')
            ->will($this->returnValue('images/file.png'));

        $this->targetDecoratorMock
            ->expects($this->once())
            ->method('decorate')
            ->with('images/file.png')
            ->will($this->returnValue('/var/test/storage/images/file.png'));

        self::assertEquals(
            '/var/test/storage/images/file.png',
            $this->redecorator->redecorateFromSource('http://static.example.com/images/file.png')
        );
    }

    public function testRedecorateFromTarget()
    {
        $this->targetDecoratorMock
            ->expects($this->once())
            ->method('undecorate')
            ->with('/var/test/storage/images/file.png')
            ->will($this->returnValue('images/file.png'));

        $this->sourceDecoratorMock
            ->expects($this->once())
            ->method('decorate')
            ->with('images/file.png')
            ->will($this->returnValue('http://static.example.com/images/file.png'));

        self::assertEquals(
            'http://static.example.com/images/file.png',
            $this->redecorator->redecorateFromTarget('/var/test/storage/images/file.png')
        );
    }
}

class_alias(UrlRedecoratorTest::class, 'eZ\Publish\Core\IO\Tests\UrlRedecoratorTest');
