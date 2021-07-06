<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests\Iterator;

use eZ\Publish\API\Repository\Iterator\BatchIterator;
use PHPUnit\Framework\TestCase;

final class BatchIteratorTest extends TestCase
{
    public function testIterateOverDummyResultSet(): void
    {
        $expectedData = range(1, 100);

        $iterator = new BatchIterator(new BatchIteratorTestAdapter($expectedData));
        $iterator->setBatchSize(7);

        $this->assertEquals($expectedData, iterator_to_array($iterator));
    }

    public function testIterateOverEmptyResultSet(): void
    {
        $iterator = new BatchIterator(new BatchIteratorTestAdapter([]));
        $iterator->setBatchSize(10);

        $this->assertEquals([], iterator_to_array($iterator));
    }
}
