<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

return SearchResult::__set_state([
    'facets' => [],
    'searchHits' => [
            SearchHit::__set_state([
                'valueObject' => [
                        'id' => 10,
                        'title' => 'Anonymous User',
                    ],
                'score' => null,
                'index' => null,
                'highlight' => null,
                'matchedTranslation' => 'eng-US',
            ]),
        ],
    'spellSuggestion' => null,
    'time' => 1,
    'timedOut' => null,
    'maxScore' => null,
    'totalCount' => 1,
]);
