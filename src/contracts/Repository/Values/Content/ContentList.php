<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * A filtered Content items list iterator.
 */
final class ContentList implements IteratorAggregate
{
    /** @var int */
    private $totalCount;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content[] */
    private $contentItems;

    /**
     * @internal for internal use by Repository
     */
    public function __construct(int $totalCount, array $contentItems)
    {
        $this->totalCount = $totalCount;
        $this->contentItems = $contentItems;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]|\Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->contentItems);
    }
}

class_alias(ContentList::class, 'eZ\Publish\API\Repository\Values\Content\ContentList');
