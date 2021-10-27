<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\Filter;

use Ibexa\Bundle\Core\Imagine\Filter\UnsupportedFilter;
use Imagine\Image\ImageInterface;

class UnsupportedFilterTest extends AbstractFilterTest
{
    public function testLoad()
    {
        $this->expectException(\Imagine\Exception\NotSupportedException::class);

        $filter = new UnsupportedFilter();
        $filter->apply($this->createMock(ImageInterface::class));
    }
}

class_alias(UnsupportedFilterTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\Filter\UnsupportedFilterTest');
