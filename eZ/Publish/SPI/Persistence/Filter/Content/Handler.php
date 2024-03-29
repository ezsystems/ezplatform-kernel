<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Persistence\Filter\Content;

use eZ\Publish\API\Repository\Values\Filter\Filter;

/**
 * Content Filtering ContentHandler.
 *
 * @internal for internal use by Repository
 */
interface Handler
{
    /**
     * @return \eZ\Publish\SPI\Persistence\Filter\Content\LazyContentItemListIterator
     */
    public function find(Filter $filter): iterable;

    public function count(Filter $filter): int;
}
