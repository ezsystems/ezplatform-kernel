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
                    'score' => 0.49504447000000001,
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
                    'score' => 0.49504447000000001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            2 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 52,
                        'title' => 'Common INI settings',
                    ],
                    'score' => 0.18718657,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            3 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 54,
                        'title' => 'eZ Publish Demo Design (without demo content)',
                    ],
                    'score' => 0.18718657,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            4 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 56,
                        'title' => 'Design',
                    ],
                    'score' => 0.49504447000000001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            5 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 57,
                        'title' => 'Home',
                    ],
                    'score' => 1.8913484,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            6 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 58,
                        'title' => 'Contact Us',
                    ],
                    'score' => 0.81501900000000005,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
            7 => SearchHit::__set_state(
                [
                    'valueObject' => [
                        'id' => 59,
                        'title' => 'Partners',
                    ],
                    'score' => 0.49504447000000001,
                    'index' => null,
                    'highlight' => null,
                ]
            ),
        ],
        'spellSuggestion' => null,
        'time' => 1,
        'timedOut' => null,
        'maxScore' => 1.8913484,
        'totalCount' => 18,
    ]
);
