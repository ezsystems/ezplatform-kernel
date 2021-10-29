<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Trash;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Traversable;

class TrashItemDeleteResultList extends ValueObject implements \IteratorAggregate
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult[] */
    public $items = [];

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class_alias(TrashItemDeleteResultList::class, 'eZ\Publish\API\Repository\Values\Content\Trash\TrashItemDeleteResultList');
