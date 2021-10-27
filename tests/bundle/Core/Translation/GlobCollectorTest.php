<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Translation;

use Ibexa\Bundle\Core\Translation\GlobCollector;
use PHPUnit\Framework\TestCase;

class GlobCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $collector = new GlobCollector(__DIR__ . '/../Resources/Translation');

        $files = $collector->collect();
        self::assertCount(3, $files);
        foreach ($files as $file) {
            self::assertContains($file['domain'], ['messages', 'dashboard']);
            self::assertContains($file['locale'], ['fr', 'ach_UG']);
            self::assertEquals('xlf', $file['format']);
        }
    }
}

class_alias(GlobCollectorTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Translation\GlobCollectorTest');
