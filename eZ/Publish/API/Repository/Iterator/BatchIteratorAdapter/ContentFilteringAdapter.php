<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use Iterator;

final class ContentFilteringAdapter implements BatchIteratorAdapter
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\Values\Filter\Filter */
    private $filter;

    /** @var array|null */
    private $languages;

    public function __construct(ContentService $contentService, Filter $filter, ?array $languages = null)
    {
        $this->contentService = $contentService;
        $this->filter = $filter;
        $this->languages = $languages;
    }

    public function fetch(int $offset, int $limit): Iterator
    {
        $filter = clone $this->filter;
        $filter->sliceBy($limit, $offset);

        return $this->contentService->find($filter, $this->languages)->getIterator();
    }
}
