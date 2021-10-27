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
    public function testCollect()
    {
        $translationRootDir = str_replace(
            sprintf('%1$sTests%1$sTranslation', \DIRECTORY_SEPARATOR),
            sprintf('%1$sTests%1$sResources%1$sTranslation', \DIRECTORY_SEPARATOR),
            __DIR__
        );
        $collector = new GlobCollector($translationRootDir);

        $files = $collector->collect();
        $this->assertCount(3, $files);
        foreach ($files as $file) {
            $this->assertTrue(in_array($file['domain'], ['messages', 'dashboard']));
            $this->assertTrue(in_array($file['locale'], ['fr', 'ach_UG']));
            $this->assertEquals($file['format'], 'xlf');
        }
    }
}

class_alias(GlobCollectorTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Translation\GlobCollectorTest');
