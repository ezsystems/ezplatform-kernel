<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\VariationPathGenerator;

use Ibexa\Bundle\Core\Imagine\VariationPathGenerator\OriginalDirectoryVariationPathGenerator;
use PHPUnit\Framework\TestCase;

class OriginalDirectoryVariationPathGeneratorTest extends TestCase
{
    public function testGetVariationPath()
    {
        $generator = new OriginalDirectoryVariationPathGenerator();
        self::assertEquals(
            'path/to/original_large.png',
            $generator->getVariationPath('path/to/original.png', 'large')
        );
    }
}

class_alias(OriginalDirectoryVariationPathGeneratorTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\VariationPathGenerator\OriginalDirectoryVariationPathGeneratorTest');
