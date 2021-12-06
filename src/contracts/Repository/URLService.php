<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

use Ibexa\Contracts\Core\Repository\Values\URL\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult;

/**
 * URL Service.
 */
interface URLService
{
    /**
     * Instantiates a new URL update struct.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct
     */
    public function createUpdateStruct(): URLUpdateStruct;

    /**
     * Find URLs.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URLQuery $query
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\SearchResult
     */
    public function findUrls(URLQuery $query): SearchResult;

    /**
     * Find content objects using URL.
     *
     * Content is filter by user permissions.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URL $url
     * @param int $offset
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\UsageSearchResult
     */
    public function findUsages(URL $url, int $offset = 0, int $limit = -1): UsageSearchResult;

    /**
     * Load single URL (by ID).
     *
     * @param int $id ID of URL
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\URL
     */
    public function loadById(int $id): URL;

    /**
     * Load single URL (by URL).
     *
     * @param string $url URL
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\URL
     */
    public function loadByUrl(string $url): URL;

    /**
     * Updates URL.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URL $url
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct $struct
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the url already exists
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\URL
     */
    public function updateUrl(URL $url, URLUpdateStruct $struct): URL;
}

class_alias(URLService::class, 'eZ\Publish\API\Repository\URLService');
