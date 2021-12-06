<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\SearchService as SearchServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location as LocationCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalOperator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location as LocationSortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Search\Capable;
use Ibexa\Contracts\Core\Search\Handler;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Mapper\ContentDomainMapper;
use Ibexa\Core\Search\Common\BackgroundIndexer;

/**
 * Search service.
 */
class SearchService implements SearchServiceInterface
{
    /** @var \Ibexa\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Contracts\Core\Search\Handler */
    protected $searchHandler;

    /** @var array */
    protected $settings;

    /** @var \Ibexa\Core\Repository\Mapper\ContentDomainMapper */
    protected $contentDomainMapper;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver */
    protected $permissionCriterionResolver;

    /** @var \Ibexa\Core\Search\Common\BackgroundIndexer */
    protected $backgroundIndexer;

    /**
     * Setups service with reference to repository object that created it & corresponding handler.
     *
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\Contracts\Core\Search\Handler $searchHandler
     * @param \Ibexa\Core\Repository\Mapper\ContentDomainMapper $contentDomainMapper
     * @param \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver $permissionCriterionResolver
     * @param \Ibexa\Core\Search\Common\BackgroundIndexer $backgroundIndexer
     * @param array $settings
     */
    public function __construct(
        RepositoryInterface $repository,
        Handler $searchHandler,
        ContentDomainMapper $contentDomainMapper,
        PermissionCriterionResolver $permissionCriterionResolver,
        BackgroundIndexer $backgroundIndexer,
        array $settings = []
    ) {
        $this->repository = $repository;
        $this->searchHandler = $searchHandler;
        $this->contentDomainMapper = $contentDomainMapper;
        // Union makes sure default settings are ignored if provided in argument
        $this->settings = $settings + [
            //'defaultSetting' => array(),
        ];
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        $this->backgroundIndexer = $backgroundIndexer;
    }

    /**
     * Finds content objects for the given query.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if query is not valid
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     * @param array $languageFilter Configuration for specifying prioritized languages query will be performed on.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations.
     * @param bool $filterOnUserPermissions if true only the objects which the user is allowed to read are returned.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult
     */
    public function findContent(Query $query, array $languageFilter = [], bool $filterOnUserPermissions = true): SearchResult
    {
        $result = $this->internalFindContentInfo($query, $languageFilter, $filterOnUserPermissions);
        $missingContentList = $this->contentDomainMapper->buildContentDomainObjectsOnSearchResult($result, $languageFilter);
        foreach ($missingContentList as $missingContent) {
            $this->backgroundIndexer->registerContent($missingContent);
        }

        return $result;
    }

    /**
     * Finds contentInfo objects for the given query.
     *
     * @see \Ibexa\Contracts\Core\Repository\SearchService::findContentInfo()
     * @since 5.4.5
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if query is not valid
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     * @param array $languageFilter - a map of filters for the returned fields.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations.
     * @param bool $filterOnUserPermissions if true (default) only the objects which is the user allowed to read are returned.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult
     */
    public function findContentInfo(Query $query, array $languageFilter = [], bool $filterOnUserPermissions = true): SearchResult
    {
        $result = $this->internalFindContentInfo($query, $languageFilter, $filterOnUserPermissions);
        foreach ($result->searchHits as $hit) {
            $hit->valueObject = $this->contentDomainMapper->buildContentInfoDomainObject(
                $hit->valueObject
            );
        }

        return $result;
    }

    /**
     * Finds SPI content info objects for the given query.
     *
     * Internal for use by {@link findContent} and {@link findContentInfo}.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if query is not valid
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     * @param array $languageFilter - a map of filters for the returned fields.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations.
     * @param bool $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult With "raw" SPI contentInfo objects in result
     */
    protected function internalFindContentInfo(Query $query, array $languageFilter = [], $filterOnUserPermissions = true)
    {
        if (!is_int($query->offset)) {
            throw new InvalidArgumentType(
                '$query->offset',
                'integer',
                $query->offset
            );
        }

        if (!is_int($query->limit)) {
            throw new InvalidArgumentType(
                '$query->limit',
                'integer',
                $query->limit
            );
        }

        $query = clone $query;
        $query->filter = $query->filter ?: new Criterion\MatchAll();

        $this->validateContentCriteria([$query->query], '$query');
        $this->validateContentCriteria([$query->filter], '$query');
        $this->validateContentSortClauses($query);

        if ($filterOnUserPermissions && !$this->addPermissionsCriterion($query->filter)) {
            return new SearchResult(['time' => 0, 'totalCount' => 0]);
        }

        return $this->searchHandler->findContent($query, $languageFilter);
    }

    /**
     * Checks that $criteria does not contain Location criterions.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     * @param string $argumentName
     */
    protected function validateContentCriteria(array $criteria, $argumentName)
    {
        foreach ($criteria as $criterion) {
            if ($criterion instanceof LocationCriterion) {
                throw new InvalidArgumentException(
                    $argumentName,
                    'Location Criteria cannot be used in Content search'
                );
            }
            if ($criterion instanceof LogicalOperator) {
                $this->validateContentCriteria($criterion->criteria, $argumentName);
            }
        }
    }

    /**
     * Checks that $query does not contain Location sort clauses.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     */
    protected function validateContentSortClauses(Query $query)
    {
        foreach ($query->sortClauses as $sortClause) {
            if ($sortClause instanceof LocationSortClause) {
                throw new InvalidArgumentException('$query', 'Location Sort Clauses cannot be used in Content search');
            }
        }
    }

    /**
     * Performs a query for a single content object.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the object was not found by the query or due to permissions
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if criterion is not valid
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if there is more than one result matching the criterions
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter
     * @param array $languageFilter Configuration for specifying prioritized languages query will be performed on.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations.
     * @param bool $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function findSingle(Criterion $filter, array $languageFilter = [], bool $filterOnUserPermissions = true): Content
    {
        $this->validateContentCriteria([$filter], '$filter');

        if ($filterOnUserPermissions && !$this->addPermissionsCriterion($filter)) {
            throw new NotFoundException('Content', '*');
        }

        $contentInfo = $this->searchHandler->findSingle($filter, $languageFilter);

        return $this->repository->getContentService()->internalLoadContentById(
            $contentInfo->id,
            (!empty($languageFilter['languages']) ? $languageFilter['languages'] : null),
            null,
            (isset($languageFilter['useAlwaysAvailable']) ? $languageFilter['useAlwaysAvailable'] : true)
        );
    }

    /**
     * Suggests a list of values for the given prefix.
     *
     * @param string $prefix
     * @param string[] $fieldPaths
     * @param int $limit
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion|null $filter
     */
    public function suggest(string $prefix, array $fieldPaths = [], int $limit = 10, Criterion $filter = null)
    {
    }

    /**
     * Finds Locations for the given query.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if query is not valid
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery $query
     * @param array $languageFilter Configuration for specifying prioritized languages query will be performed on.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations
     * @param bool $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult
     */
    public function findLocations(LocationQuery $query, array $languageFilter = [], bool $filterOnUserPermissions = true): SearchResult
    {
        if (!is_int($query->offset)) {
            throw new InvalidArgumentType(
                '$query->offset',
                'integer',
                $query->offset
            );
        }

        if (!is_int($query->limit)) {
            throw new InvalidArgumentType(
                '$query->limit',
                'integer',
                $query->limit
            );
        }

        $query = clone $query;
        $query->filter = $query->filter ?: new Criterion\MatchAll();

        if ($filterOnUserPermissions && !$this->addPermissionsCriterion($query->filter)) {
            return new SearchResult(['time' => 0, 'totalCount' => 0]);
        }

        $result = $this->searchHandler->findLocations($query, $languageFilter);

        $missingLocations = $this->contentDomainMapper->buildLocationDomainObjectsOnSearchResult($result, $languageFilter);
        foreach ($missingLocations as $missingLocation) {
            $this->backgroundIndexer->registerLocation($missingLocation);
        }

        return $result;
    }

    /**
     * Adds content, read Permission criteria if needed and return false if no access at all.
     *
     * @uses \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver::getPermissionsCriterion()
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    protected function addPermissionsCriterion(Criterion &$criterion): bool
    {
        $permissionCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'read');
        if ($permissionCriterion === true || $permissionCriterion === false) {
            return $permissionCriterion;
        }

        // Merge with original $criterion
        if ($criterion instanceof LogicalAnd) {
            $criterion->criteria[] = $permissionCriterion;
        } else {
            $criterion = new LogicalAnd(
                [
                    $criterion,
                    $permissionCriterion,
                ]
            );
        }

        return true;
    }

    public function supports(int $capabilityFlag): bool
    {
        if ($this->searchHandler instanceof Capable) {
            return $this->searchHandler->supports($capabilityFlag);
        }

        return false;
    }
}

class_alias(SearchService::class, 'eZ\Publish\Core\Repository\SearchService');
