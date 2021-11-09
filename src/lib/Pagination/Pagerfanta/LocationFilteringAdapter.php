<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Pagerfanta\Adapter\AdapterInterface;

final class LocationFilteringAdapter implements AdapterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\Filter */
    private $filter;

    /** @var array|null */
    private $languageFilter;

    /** @var int|null */
    private $totalCount;

    public function __construct(
        LocationService $locationService,
        Filter $filter,
        ?array $languageFilter = null
    ) {
        $this->locationService = $locationService;
        $this->filter = $filter;
        $this->languageFilter = $languageFilter;
    }

    public function getNbResults(): int
    {
        if ($this->totalCount === null) {
            $countFilter = clone $this->filter;
            $countFilter->sliceBy(0, 0);

            $this->totalCount = $this->locationService->find(
                $countFilter,
                $this->languageFilter
            )->totalCount;
        }

        return $this->totalCount;
    }

    public function getSlice($offset, $length): iterable
    {
        $selectFilter = clone $this->filter;
        $selectFilter->sliceBy($length, $offset);

        $results = $this->locationService->find($selectFilter, $this->languageFilter);
        if ($this->totalCount === null) {
            $this->totalCount = $results->totalCount;
        }

        return $results;
    }
}

class_alias(LocationFilteringAdapter::class, 'eZ\Publish\Core\Pagination\Pagerfanta\LocationFilteringAdapter');
