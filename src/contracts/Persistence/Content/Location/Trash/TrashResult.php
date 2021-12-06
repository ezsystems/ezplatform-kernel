<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content\Location\Trash;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class TrashResult extends ValueObject implements \IteratorAggregate
{
    /**
     * The total number of Trash items matching criteria (ignores offset & limit arguments).
     *
     * @var int
     */
    public $totalCount = 0;

    /**
     * The value objects found for the query.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\Location\Trashed[]
     */
    public $items = [];

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class_alias(TrashResult::class, 'eZ\Publish\SPI\Persistence\Content\Location\Trash\TrashResult');
