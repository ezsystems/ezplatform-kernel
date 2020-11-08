<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use Pagerfanta\Adapter\AdapterInterface;

final class LocationFilteringAdapter implements AdapterInterface
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\Values\Filter\Filter */
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
            $filter = clone $this->filter;
            $filter->sliceBy(0, 0);

            $this->totalCount = $this->locationService->find(
                $filter,
                $this->languageFilter
            )->totalCount;
        }

        return $this->totalCount;
    }

    public function getSlice($offset, $length): iterable
    {
        $filter = clone $this->filter;
        $filter->sliceBy($length, $offset);

        $results = $this->locationService->find($filter, $this->languageFilter);
        if ($this->totalCount === null) {
            $this->totalCount = $results->totalCount;
        }

        return $results;
    }
}
