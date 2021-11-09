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
                        'id' => 50,
                        'title' => 'Files',
                    ],
                    'score' => 0.20774001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            1 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 51,
                        'title' => 'Multimedia',
                    ],
                    'score' => 0.20774001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            2 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 56,
                        'title' => 'Design',
                    ],
                    'score' => 0.20774001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            3 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 57,
                        'title' => 'Home',
                    ],
                    'score' => 3.007218,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            4 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 58,
                        'title' => 'Contact Us',
                    ],
                    'score' => 1.295869,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            5 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 59,
                        'title' => 'Partners',
                    ],
                    'score' => 0.20774001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
        ],
        'spellSuggestion' => null,
        'time' => 1,
        'timedOut' => null,
        'maxScore' => 3.007218,
        'totalCount' => 16,
    ]
);
