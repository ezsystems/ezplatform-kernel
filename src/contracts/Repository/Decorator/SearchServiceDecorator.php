<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

abstract class SearchServiceDecorator implements SearchService
{
    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    protected $innerService;

    public function __construct(SearchService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function findContent(
        Query $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): SearchResult {
        return $this->innerService->findContent($query, $languageFilter, $filterOnUserPermissions);
    }

    public function findContentInfo(
        Query $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): SearchResult {
        return $this->innerService->findContentInfo($query, $languageFilter, $filterOnUserPermissions);
    }

    public function findSingle(
        Criterion $filter,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): Content {
        return $this->innerService->findSingle($filter, $languageFilter, $filterOnUserPermissions);
    }

    public function suggest(
        string $prefix,
        array $fieldPaths = [],
        int $limit = 10,
        Criterion $filter = null
    ) {
        return $this->innerService->suggest($prefix, $fieldPaths, $limit, $filter);
    }

    public function findLocations(
        LocationQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): SearchResult {
        return $this->innerService->findLocations($query, $languageFilter, $filterOnUserPermissions);
    }

    public function supports(int $capabilityFlag): bool
    {
        return $this->innerService->supports($capabilityFlag);
    }
}

class_alias(SearchServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\SearchServiceDecorator');
