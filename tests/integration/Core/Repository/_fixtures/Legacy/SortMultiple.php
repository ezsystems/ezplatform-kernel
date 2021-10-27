<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

return SearchResult::__set_state([
   'facets' => [
  ],
   'searchHits' => [
    0 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 51,
        'title' => 'Multimedia',
      ],
       'score' => null,
       'index' => null,
       'highlight' => null,
    ]),
    1 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 50,
        'title' => 'Files',
      ],
       'score' => null,
       'index' => null,
       'highlight' => null,
    ]),
    2 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 49,
        'title' => 'Images',
      ],
       'score' => null,
       'index' => null,
       'highlight' => null,
    ]),
    3 => SearchHit::__set_state([
        'valueObject' => [
            'id' => 41,
            'title' => 'Media',
        ],
        'score' => null,
        'index' => null,
        'highlight' => null,
    ]),
  ],
   'spellSuggestion' => null,
   'time' => 1,
   'timedOut' => null,
   'maxScore' => null,
   'totalCount' => 4,
]);
