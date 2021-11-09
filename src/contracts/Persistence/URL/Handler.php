<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\URL;

use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;

/**
 * The URL Handler interface defines operations on URLs in the storage engine.
 */
interface Handler
{
    /**
     * Updates a existing URL.
     *
     * @param int $id
     * @param \Ibexa\Contracts\Core\Persistence\URL\URLUpdateStruct $urlUpdateStruct
     *
     * @return \Ibexa\Contracts\Core\Persistence\URL\URL
     */
    public function updateUrl($id, URLUpdateStruct $urlUpdateStruct);

    /**
     * Selects URLs data using $query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URLQuery $query
     *
     * @return array
     */
    public function find(URLQuery $query);

    /**
     * Returns IDs of Content Objects using URL identified by $id.
     *
     * @param int $id
     *
     * @return array
     */
    public function findUsages($id);

    /**
     * Loads the data for the URL identified by $id.
     *
     * @param int $id
     *
     * @return \Ibexa\Contracts\Core\Persistence\URL\URL
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function loadById($id);

    /**
     * Loads the data for the URL identified by $url.
     *
     * @param string $url
     *
     * @return \Ibexa\Contracts\Core\Persistence\URL\URL
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function loadByUrl($url);
}

class_alias(Handler::class, 'eZ\Publish\SPI\Persistence\URL\Handler');
