<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\VariationPathGenerator;

use Ibexa\Bundle\Core\Imagine\VariationPathGenerator\AliasDirectoryVariationPathGenerator;
use PHPUnit\Framework\TestCase;

class AliasDirectoryVariationPathGeneratorTest extends TestCase
{
    public function testGetVariationPath()
    {
        $generator = new AliasDirectoryVariationPathGenerator();

        self::assertEquals(
            '_aliases/large/path/to/original.png',
            $generator->getVariationPath('path/to/original.png', 'large')
        );
    }
}

class_alias(AliasDirectoryVariationPathGeneratorTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\VariationPathGenerator\AliasDirectoryVariationPathGeneratorTest');
