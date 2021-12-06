<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Filter\Location;

use Ibexa\Contracts\Core\Persistence\Filter\LazyListIterator;

/**
 * SPI Persistence Content Item list iterator.
 *
 * @internal for internal use by Repository Filtering
 *
 * @see \Ibexa\Contracts\Core\Repository\Values\Content\LocationList
 */
class LazyLocationListIterator extends LazyListIterator
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationList[]
     *
     * @throws \Exception
     */
    public function getIterator(): iterable
    {
        yield from parent::getIterator();
    }
}

class_alias(LazyLocationListIterator::class, 'eZ\Publish\SPI\Persistence\Filter\Location\LazyLocationListIterator');
