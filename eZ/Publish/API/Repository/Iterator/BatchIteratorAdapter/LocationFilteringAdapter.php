<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;

use eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use Iterator;

final class LocationFilteringAdapter implements BatchIteratorAdapter
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\Values\Filter\Filter */
    private $filter;

    /** @var string[]|null */
    private $languages;

    public function __construct(LocationService $locationService, Filter $filter, ?array $languages = null)
    {
        $this->locationService = $locationService;
        $this->filter = $filter;
        $this->languages = $languages;
    }

    public function fetch(int $offset, int $limit): Iterator
    {
        $filter = clone $this->filter;
        $filter->sliceBy($limit, $offset);

        return $this->locationService->find($filter, $this->languages)->getIterator();
    }
}
