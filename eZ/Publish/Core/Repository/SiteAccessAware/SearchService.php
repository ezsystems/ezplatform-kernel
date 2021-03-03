<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\SiteAccessAware;

use eZ\Publish\API\Repository\SearchService as SearchServiceInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Location\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\LanguageResolver;

/**
 * SiteAccess aware implementation of SearchService injecting languages where needed.
 */
class SearchService implements SearchServiceInterface
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $service;

    /** @var \eZ\Publish\API\Repository\LanguageResolver */
    protected $languageResolver;

    /**
     * Construct service object from aggregated service and LanguageResolver.
     *
     * @param \eZ\Publish\API\Repository\SearchService $service
     * @param \eZ\Publish\API\Repository\LanguageResolver $languageResolver
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
