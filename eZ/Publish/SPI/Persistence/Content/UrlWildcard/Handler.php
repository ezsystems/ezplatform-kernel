<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Persistence\Content\UrlWildcard;

use eZ\Publish\SPI\Persistence\Content\UrlWildcard;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\URLWildcardQuery;

/**
 * The UrlWildcard Handler interface provides nice urls with wildcards management.
 *
 * Its methods operate on a representation of the url alias data structure held
 * inside a storage engine.
 */
interface Handler
{
    /**
     * Creates a new url wildcard.
     *
     * @param string $sourceUrl
     * @param string $destinationUrl
     * @param bool $forward
     *
     * @return \eZ\Publish\SPI\Persistence\Content\UrlWildcard
     */
    public function create($sourceUrl, $destinationUrl, $forward = false);

    public function update(
        int $id,
        string $sourceUrl,
        string $destinationUrl,
        bool $forward
    ): UrlWildcard;

    /**
     * removes an url wildcard.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the url wild card was not found
     *
     * @param mixed $id
     */
    public function remove($id);

    /**
     * Loads a url wild card.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the url wild card was not found
     *
     * @param mixed $id
     *
     * @return \eZ\Publish\SPI\Persistence\Content\UrlWildcard
     */
    public function load($id);

    /**
     * Loads all url wild card (paged).
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \eZ\Publish\SPI\Persistence\Content\UrlWildcard[]
     */
    public function loadAll($offset = 0, $limit = -1);

    /**
     * Find URLWildcards.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function find(URLWildcardQuery $query): array;

    /**
     * Performs lookup for given (source) URL.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the url wild card was not found
     *
     * @param string $sourceUrl
     *
     * @return \eZ\Publish\SPI\Persistence\Content\UrlWildcard
     */
    public function translate(string $sourceUrl): UrlWildcard;

    /**
     * Checks whether UrlWildcard with given source url exits.
     *
     * @param string $sourceUrl
     *
     * @return bool
     */
    public function exactSourceUrlExists(string $sourceUrl): bool;

    /**
     * Counts URL Wildcards.
     */
    public function countAll(): int;
}
