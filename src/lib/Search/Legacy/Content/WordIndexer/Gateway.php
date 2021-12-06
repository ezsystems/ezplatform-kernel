<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\WordIndexer;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Core\Search\Legacy\Content\FullTextData;

/**
 * The WordIndexer Gateway abstracts indexing of content full text data.
 */
abstract class Gateway
{
    /**
     * Index search engine FullTextData objects corresponding to content object field values.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\FullTextData $fullTextValue
     */
    abstract public function index(FullTextData $fullTextValue);

    /**
     * Remove whole content or a specific version from index.
     *
     * @param mixed      $contentId
     * @param mixed|null $versionId
     */
    abstract public function remove($contentId, $versionId = null);

    /**
     * Indexes an array of FullTextData objects.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\FullTextData[] $fullTextBulkData
     */
    abstract public function bulkIndex(array $fullTextBulkData);

    /**
     * Remove entire search index.
     */
    abstract public function purgeIndex();
}

class_alias(Gateway::class, 'eZ\Publish\Core\Search\Legacy\Content\WordIndexer\Gateway');
