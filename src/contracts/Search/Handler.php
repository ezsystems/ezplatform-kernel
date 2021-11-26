<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * The Search handler retrieves sets of of Content objects, based on a
 * set of criteria.
 */
interface Handler
{
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
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult With ContentInfo as SearchHit->valueObject
     */
    public function findContent(Query $query, array $languageFilter = []);

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
    public function findSingle(Criterion $filter, array $languageFilter = []);

    /**
     * Finds locations for the given $query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery $query
     * @param array $languageFilter - a map of language related filters specifying languages query will be performed on.
     *        Also used to define which field languages are loaded for the returned content.
     *        Currently supports: <code>array("languages" => array(<language1>,..), "useAlwaysAvailable" => bool)</code>
     *                            useAlwaysAvailable defaults to true to avoid exceptions on missing translations
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult With Location as SearchHit->valueObject
     */
    public function findLocations(LocationQuery $query, array $languageFilter = []);

    /**
     * Suggests a list of values for the given prefix.
     *
     * @param string $prefix
     * @param string[] $fieldPaths
     * @param int $limit
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion|null $filter
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, Criterion $filter = null);

    /**
     * Indexes a content object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content $content
     */
    public function indexContent(Content $content);

    /**
     * Deletes a content object from the index.
     *
     * @param int $contentId
     * @param int|null $versionId
     */
    public function deleteContent($contentId, $versionId = null);

    /**
     * Indexes a Location in the index storage.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Location $location
     */
    public function indexLocation(Location $location);

    /**
     * Deletes a location from the index.
     *
     * @param mixed $locationId
     * @param mixed $contentId
     */
    public function deleteLocation($locationId, $contentId);

    /**
     * Purges all contents from the index.
     */
    public function purgeIndex();
}

class_alias(Handler::class, 'eZ\Publish\SPI\Search\Handler');
