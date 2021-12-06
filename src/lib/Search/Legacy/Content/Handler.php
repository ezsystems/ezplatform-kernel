<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Search\VersatileHandler as SearchHandlerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Persistence\Legacy\Content\Location\Mapper as LocationMapper;
use Ibexa\Core\Persistence\Legacy\Content\Mapper as ContentMapper;
use Ibexa\Core\Search\Legacy\Content\Location\Gateway as LocationGateway;
use Ibexa\Core\Search\Legacy\Content\Mapper\FullTextMapper;
use Ibexa\Core\Search\Legacy\Content\WordIndexer\Gateway as WordIndexerGateway;

/**
 * The Content Search handler retrieves sets of of Content objects, based on a
 * set of criteria.
 *
 * The basic idea of this class is to do the following:
 *
 * 1) The find methods retrieve a recursive set of filters, which define which
 * content objects to retrieve from the database. Those may be combined using
 * boolean operators.
 *
 * 2) This recursive criterion definition is visited into a query, which limits
 * the content retrieved from the database. We might not be able to create
 * sensible queries from all criterion definitions.
 *
 * 3) The query might be possible to optimize (remove empty statements),
 * reduce singular and and or constructs…
 *
 * 4) Additionally we might need a post-query filtering step, which filters
 * content objects based on criteria, which could not be converted in to
 * database statements.
 */
class Handler implements SearchHandlerInterface
{
    /**
     * Content locator gateway.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Gateway
     */
    protected $gateway;

    /**
     * Location locator gateway.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Location\Gateway
     */
    protected $locationGateway;

    /**
     * Word indexer gateway.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\WordIndexer\Gateway
     */
    protected $indexerGateway;

    /**
     * Content mapper.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Mapper
     */
    protected $contentMapper;

    /**
     * Location locationMapper.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Location\Mapper
     */
    protected $locationMapper;

    /**
     * Language handler.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\Language\Handler
     */
    protected $languageHandler;

    /**
     * FullText mapper.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Mapper\FullTextMapper
     */
    protected $mapper;

    public function __construct(
        Gateway $gateway,
        LocationGateway $locationGateway,
        WordIndexerGateway $indexerGateway,
        ContentMapper $contentMapper,
        LocationMapper $locationMapper,
        LanguageHandler $languageHandler,
        FullTextMapper $mapper
    ) {
        $this->gateway = $gateway;
        $this->locationGateway = $locationGateway;
        $this->indexerGateway = $indexerGateway;
        $this->contentMapper = $contentMapper;
        $this->locationMapper = $locationMapper;
        $this->languageHandler = $languageHandler;
        $this->mapper = $mapper;
    }

    /**
     * Finds content objects for the given query.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if Query criterion is not applicable to its target
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     * @param array $languageFilter - a map of language related filters specifying languages query will be performed on.
     *        Also used to define which field languages are loaded for the returned content.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult
     */
    public function findContent(Query $query, array $languageFilter = [])
    {
        if (!isset($languageFilter['languages'])) {
            $languageFilter['languages'] = [];
        }

        if (!isset($languageFilter['useAlwaysAvailable'])) {
            $languageFilter['useAlwaysAvailable'] = true;
        }

        $start = microtime(true);
        $query->filter = $query->filter ?: new Criterion\MatchAll();
        $query->query = $query->query ?: new Criterion\MatchAll();

        // The legacy search does not know about scores, so that we just
        // combine the query with the filter
        $filter = new Criterion\LogicalAnd([$query->query, $query->filter]);

        $data = $this->gateway->find(
            $filter,
            $query->offset,
            $query->limit,
            $query->sortClauses,
            $languageFilter,
            $query->performCount
        );

        $result = new SearchResult();
        $result->time = microtime(true) - $start;
        $result->totalCount = $data['count'];
        $contentInfoList = $this->contentMapper->extractContentInfoFromRows(
            $data['rows'],
            '',
            'main_tree_'
        );

        foreach ($contentInfoList as $index => $contentInfo) {
            $searchHit = new SearchHit();
            $searchHit->valueObject = $contentInfo;
            $searchHit->matchedTranslation = $this->extractMatchedLanguage(
                $data['rows'][$index]['language_mask'],
                $data['rows'][$index]['initial_language_id'],
                $languageFilter
            );

            $result->searchHits[] = $searchHit;
        }

        return $result;
    }

    protected function extractMatchedLanguage($languageMask, $mainLanguageId, $languageSettings)
    {
        $languageList = !empty($languageSettings['languages']) ?
            $this->languageHandler->loadListByLanguageCodes($languageSettings['languages']) :
            [];

        foreach ($languageList as $language) {
            if ($languageMask & $language->id) {
                return $language->languageCode;
            }
        }

        if ($languageMask & 1 || empty($languageSettings['languages'])) {
            return $this->languageHandler->load($mainLanguageId)->languageCode;
        }

        return null;
    }

    /**
     * Performs a query for a single content object.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the object was not found by the query or due to permissions
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if Criterion is not applicable to its target
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if there is more than than one result matching the criterions
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter
     * @param array $languageFilter - a map of language related filters specifying languages query will be performed on.
     *        Also used to define which field languages are loaded for the returned content.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\ContentInfo
     */
    public function findSingle(Criterion $filter, array $languageFilter = [])
    {
        if (!isset($languageFilter['languages'])) {
            $languageFilter['languages'] = [];
        }

        if (!isset($languageFilter['useAlwaysAvailable'])) {
            $languageFilter['useAlwaysAvailable'] = true;
        }

        $searchQuery = new Query();
        $searchQuery->filter = $filter;
        $searchQuery->query = new Criterion\MatchAll();
        $searchQuery->offset = 0;
        $searchQuery->limit = 2; // Because we optimize away the count query below
        $searchQuery->performCount = true;
        $searchQuery->sortClauses = null;
        $result = $this->findContent($searchQuery, $languageFilter);

        if (empty($result->searchHits)) {
            throw new NotFoundException('Content', 'findSingle() found no content for the given $criterion');
        } elseif (isset($result->searchHits[1])) {
            throw new InvalidArgumentException('totalCount', 'findSingle() found more then one item for the given $criterion');
        }

        $first = reset($result->searchHits);

        return $first->valueObject;
    }

    public function findLocations(LocationQuery $query, array $languageFilter = [])
    {
        if (!isset($languageFilter['languages'])) {
            $languageFilter['languages'] = [];
        }

        if (!isset($languageFilter['useAlwaysAvailable'])) {
            $languageFilter['useAlwaysAvailable'] = true;
        }

        $start = microtime(true);
        $query->filter = $query->filter ?: new Criterion\MatchAll();
        $query->query = $query->query ?: new Criterion\MatchAll();

        // The legacy search does not know about scores, so we just
        // combine the query with the filter
        $data = $this->locationGateway->find(
            new Criterion\LogicalAnd([$query->query, $query->filter]),
            $query->offset,
            $query->limit,
            $query->sortClauses,
            $languageFilter,
            $query->performCount
        );

        $result = new SearchResult();
        $result->time = microtime(true) - $start;
        $result->totalCount = $data['count'];
        $locationList = $this->locationMapper->createLocationsFromRows($data['rows']);

        foreach ($locationList as $index => $location) {
            $searchHit = new SearchHit();
            $searchHit->valueObject = $location;
            $searchHit->matchedTranslation = $this->extractMatchedLanguage(
                $data['rows'][$index]['language_mask'],
                $data['rows'][$index]['initial_language_id'],
                $languageFilter
            );

            $result->searchHits[] = $searchHit;
        }

        return $result;
    }

    /**
     * Suggests a list of values for the given prefix.
     *
     * @param string $prefix
     * @param string[] $fieldPaths
     * @param int $limit
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion|null $filter
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, Criterion $filter = null)
    {
        throw new NotImplementedException('Suggestions are not supported by Legacy search engine.');
    }

    /**
     * Indexes a content object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content $content
     */
    public function indexContent(Content $content)
    {
        $fullTextValue = $this->mapper->mapContent($content);

        $this->indexerGateway->index($fullTextValue);
    }

    /**
     * Bulk index list of content objects.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content[] $contentList
     * @param callable $errorCallback (Content $content, NotFoundException $e)
     */
    public function bulkIndex(array $contentList, callable $errorCallback)
    {
        $fullTextBulkData = [];
        foreach ($contentList as $content) {
            try {
                $fullTextBulkData[] = $this->mapper->mapContent($content);
            } catch (NotFoundException $e) {
                $errorCallback($content, $e);
            }
        }

        $this->indexerGateway->bulkIndex($fullTextBulkData);
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\Location $location
     */
    public function indexLocation(Location $location)
    {
        // Not needed with Legacy Storage/Search Engine
    }

    /**
     * Deletes a content object from the index.
     *
     * @param int $contentId
     * @param int|null $versionId
     */
    public function deleteContent($contentId, $versionId = null)
    {
        $this->indexerGateway->remove($contentId, $versionId);
    }

    public function deleteTranslation(int $contentId, string $languageCode): void
    {
        // Not needed with Legacy Storage/Search Engine
    }

    /**
     * Deletes a location from the index.
     *
     * @param mixed $locationId
     * @param mixed $contentId
     */
    public function deleteLocation($locationId, $contentId)
    {
        // Not needed with Legacy Storage/Search Engine
    }

    /**
     * Purges all contents from the index.
     */
    public function purgeIndex()
    {
        $this->indexerGateway->purgeIndex();
    }

    /**
     * Commits the data to the index, making it available for search.
     *
     * @param bool $flush
     */
    public function commit($flush = false)
    {
        // Not needed with Legacy Storage/Search Engine
    }

    public function supports(int $capabilityFlag): bool
    {
        return false;
    }
}

class_alias(Handler::class, 'eZ\Publish\Core\Search\Legacy\Content\Handler');
