<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Pagerfanta adapter for content filtering.
 */
final class ContentFilteringAdapter implements AdapterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\Filter */
    private $filter;

    /** @var array|null */
    private $languageFilter;

    /** @var int|null */
    private $totalCount;

    public function __construct(
        ContentService $contentService,
        Filter $filter,
        ?array $languageFilter = null
    ) {
        $this->contentService = $contentService;
        $this->filter = $filter;
        $this->languageFilter = $languageFilter;
    }

    public function getNbResults(): int
    {
        if ($this->totalCount === null) {
            $countFilter = clone $this->filter;
            $countFilter->sliceBy(0, 0);

            $this->totalCount = $this->contentService->find(
                $countFilter,
                $this->languageFilter
            )->getTotalCount();
        }

        return $this->totalCount;
    }

    public function getSlice($offset, $length): iterable
    {
        $selectFilter = clone $this->filter;
        $selectFilter->sliceBy($length, $offset);

        $results = $this->contentService->find($selectFilter, $this->languageFilter);
        if ($this->totalCount === null) {
            $this->totalCount = $results->getTotalCount();
        }

        return $results;
    }
}

class_alias(ContentFilteringAdapter::class, 'eZ\Publish\Core\Pagination\Pagerfanta\ContentFilteringAdapter');
