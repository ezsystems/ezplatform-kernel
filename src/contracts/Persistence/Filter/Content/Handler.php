<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Filter\Content;

use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;

/**
 * Content Filtering ContentHandler.
 *
 * @internal for internal use by Repository
 */
interface Handler
{
    /**
     * @return \Ibexa\Contracts\Core\Persistence\Filter\Content\LazyContentItemListIterator
     */
    public function find(Filter $filter): iterable;
}

class_alias(Handler::class, 'eZ\Publish\SPI\Persistence\Filter\Content\Handler');
