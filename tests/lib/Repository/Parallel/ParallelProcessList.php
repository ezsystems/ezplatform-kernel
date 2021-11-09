<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Parallel;

use Jenner\SimpleFork\Process;

final class ParallelProcessList implements \IteratorAggregate
{
    /** @var \Jenner\SimpleFork\Process[] */
    private $pool = [];

    public function addProcess(Process $process): void
    {
        $this->pool[] = $process;
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->pool);
    }
}

class_alias(ParallelProcessList::class, 'eZ\Publish\API\Repository\Tests\Parallel\ParallelProcessList');
