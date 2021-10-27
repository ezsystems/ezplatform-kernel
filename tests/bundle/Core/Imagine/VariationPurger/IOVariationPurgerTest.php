<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\VariationPurger;

use Ibexa\Bundle\Core\Imagine\VariationPurger\IOVariationPurger;
use Ibexa\Core\IO\IOServiceInterface;
use PHPUnit\Framework\TestCase;

class IOVariationPurgerTest extends TestCase
{
    public function testPurgesAliasList()
    {
        $ioService = $this->createMock(IOServiceInterface::class);
        $ioService
            ->expects($this->exactly(2))
            ->method('deleteDirectory')
            ->withConsecutive(
                ['_aliases/medium'],
                ['_aliases/large']
            );
        $purger = new IOVariationPurger($ioService);
        $purger->purge(['medium', 'large']);
    }
}

class_alias(IOVariationPurgerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\VariationPurger\IOVariationPurgerTest');
