<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\URL;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Traversable;

class UsageSearchResult extends ValueObject implements \IteratorAggregate
{
    /**
     * The total number of content objects using URL.
     *
     * @var int
     */
    public $totalCount = 0;

    /**
     * The value objects found for the query.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo[]
     */
    public $items = [];

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class_alias(UsageSearchResult::class, 'eZ\Publish\API\Repository\Values\URL\UsageSearchResult');
