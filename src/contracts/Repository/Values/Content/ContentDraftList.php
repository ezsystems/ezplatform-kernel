<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use IteratorAggregate;
use Traversable;

/**
 * List of content drafts.
 */
class ContentDraftList extends ValueObject implements IteratorAggregate
{
    /**
     * @var int
     */
    public $totalCount = 0;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\DraftList\ContentDraftListItemInterface[]
     */
    public $items = [];

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class_alias(ContentDraftList::class, 'eZ\Publish\API\Repository\Values\Content\ContentDraftList');
