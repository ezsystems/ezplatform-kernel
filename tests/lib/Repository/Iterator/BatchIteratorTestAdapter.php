<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Iterator;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Iterator;

final class BatchIteratorTestAdapter implements BatchIteratorAdapter
{
    /** @var array */
    private $data;

    /** @var int */
    private $fetchCounter;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fetchCounter = 0;
    }

    public function fetch(int $offset, int $limit): Iterator
    {
        ++$this->fetchCounter;

        return new ArrayIterator(array_slice($this->data, $offset, $limit));
    }

    public function getFetchCounter(): int
    {
        return $this->fetchCounter;
    }
}

class_alias(BatchIteratorTestAdapter::class, 'eZ\Publish\API\Repository\Tests\Iterator\BatchIteratorTestAdapter');
