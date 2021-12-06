<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

return SearchResult::__set_state(
    [
        'facets' => [],
        'searchHits' => [
            0 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 4,
                        'title' => 'Users',
                    ],
                    'score' => 1.8414208999999999,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            1 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 10,
                        'title' => 'Anonymous User',
                    ],
                    'score' => 1.8414208999999999,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            2 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 14,
                        'title' => 'Administrator User',
                    ],
                    'score' => 1.384091,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            3 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 41,
                        'title' => 'Media',
                    ],
                    'score' => 1.384091,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            4 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 45,
                        'title' => 'Setup',
                    ],
                    'score' => 1.384091,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            5 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 56,
                        'title' => 'Design',
                    ],
                    'score' => 1.384091,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            6 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 57,
                        'title' => 'Home',
                    ],
                    'score' => 1.384091,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
        ],
        'spellSuggestion' => null,
        'time' => 1,
        'timedOut' => null,
        'maxScore' => 1.8414208999999999,
        'totalCount' => 7,
    ]
);
