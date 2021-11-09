<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause;

/**
 * Sets sort direction on Content ID for a content query.
 *
 * Especially useful to get reproducible search results in tests.
 *
 * Note: order will vary per search engine, depending on how Content ID is stored in the search
 * backend. For Legacy search engine IDs are stored as integers, while with Solr search engine
 * they are stored as strings. Hence the difference will be basically the one between
 * numerical and alphabetical order of sorting.
 *
 * This reflects API definition of IDs as mixed type (integer or string).
 */
class ContentId extends SortClause implements FilteringSortClause
{
    /**
     * Constructs a new ContentId SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct(string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('content_id', $sortDirection);
    }
}

class_alias(ContentId::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentId');
