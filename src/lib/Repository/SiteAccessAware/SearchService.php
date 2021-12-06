<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\SearchService as SearchServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

/**
 * SiteAccess aware implementation of SearchService injecting languages where needed.
 */
class SearchService implements SearchServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    protected $service;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageResolver */
    protected $languageResolver;

    /**
     * Construct service object from aggregated service and LanguageResolver.
     *
     * @param \Ibexa\Contracts\Core\Repository\SearchService $service
     * @param \Ibexa\Contracts\Core\Repository\LanguageResolver $languageResolver
     */
    public function __construct(
        SearchServiceInterface $service,
        LanguageResolver $languageResolver
    ) {
        $this->service = $service;
        $this->languageResolver = $languageResolver;
    }

    public function findContent(Query $query, array $languageFilter = [], bool $filterOnUserPermissions = true): SearchResult
    {
        $languageFilter['languages'] = $this->languageResolver->getPrioritizedLanguages(
            $languageFilter['languages'] ?? null
        );

        $languageFilter['useAlwaysAvailable'] = $this->languageResolver->getUseAlwaysAvailable(
            $languageFilter['useAlwaysAvailable'] ?? null
        );

        return $this->service->findContent($query, $languageFilter, $filterOnUserPermissions);
    }

    public function findContentInfo(Query $query, array $languageFilter = [], bool $filterOnUserPermissions = true): SearchResult
    {
        $languageFilter['languages'] = $this->languageResolver->getPrioritizedLanguages(
            $languageFilter['languages'] ?? null
        );

        $languageFilter['useAlwaysAvailable'] = $this->languageResolver->getUseAlwaysAvailable(
            $languageFilter['useAlwaysAvailable'] ?? null
        );

        return $this->service->findContentInfo($query, $languageFilter, $filterOnUserPermissions);
    }

    public function findSingle(Criterion $filter, array $languageFilter = [], bool $filterOnUserPermissions = true): Content
    {
        $languageFilter['languages'] = $this->languageResolver->getPrioritizedLanguages(
            $languageFilter['languages'] ?? null
        );

        $languageFilter['useAlwaysAvailable'] = $this->languageResolver->getUseAlwaysAvailable(
            $languageFilter['useAlwaysAvailable'] ?? null
        );

        return $this->service->findSingle($filter, $languageFilter, $filterOnUserPermissions);
    }

    public function suggest(string $prefix, array $fieldPaths = [], int $limit = 10, Criterion $filter = null)
    {
        return $this->service->suggest($prefix, $fieldPaths, $limit, $filter);
    }

    public function findLocations(LocationQuery $query, array $languageFilter = [], bool $filterOnUserPermissions = true): SearchResult
    {
        $languageFilter['languages'] = $this->languageResolver->getPrioritizedLanguages(
            $languageFilter['languages'] ?? null
        );

        $languageFilter['useAlwaysAvailable'] = $this->languageResolver->getUseAlwaysAvailable(
            $languageFilter['useAlwaysAvailable'] ?? null
        );

        return $this->service->findLocations($query, $languageFilter, $filterOnUserPermissions);
    }

    public function supports(int $capabilityFlag): bool
    {
        return $this->service->supports($capabilityFlag);
    }
}

class_alias(SearchService::class, 'eZ\Publish\Core\Repository\SiteAccessAware\SearchService');
