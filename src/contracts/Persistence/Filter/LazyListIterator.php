<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Filter;

use IteratorAggregate;
use RuntimeException;

/**
 * SPI Persistence Item list iterator.
 *
 * @internal for internal use by Repository Filtering
 */
abstract class LazyListIterator implements IteratorAggregate
{
    /** @var int */
    private $totalCount;

    /** @var iterable|null */
    private $iterator;

    /** @var callable|null */
    private $iterationCallback;

    public function __construct(
        int $totalCount,
        ?iterable $iterator = null,
        ?callable $iterationCallback = null
    ) {
        $this->totalCount = $totalCount;
        $this->iterator = $iterator;
        $this->iterationCallback = $iterationCallback;
    }

    public function prepareIterator(iterable $iterator, callable $iterationCallback): void
    {
        $this->iterator = $iterator;
        $this->iterationCallback = $iterationCallback;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getIterator(): iterable
    {
        if (0 === $this->totalCount) {
            yield from [];

            return;
        }

        if (null === $this->iterator || null === $this->iterationCallback) {
            throw new RuntimeException(
                "Iterator is supposed to have {$this->totalCount} elements, but there's no configured " .
                'callback to fetch them'
            );
        }

        foreach ($this->iterator as $item) {
            yield ($this->iterationCallback)($item);
        }
    }
}

class_alias(LazyListIterator::class, 'eZ\Publish\SPI\Persistence\Filter\LazyListIterator');
