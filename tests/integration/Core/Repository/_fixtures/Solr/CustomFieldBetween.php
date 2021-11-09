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
        'id' => 11,
        'title' => 'Members',
      ],
       'score' => 1,
       'index' => null,
       'matchedTranslation' => null,
       'highlight' => null,
    ]),
    1 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 12,
        'title' => 'Administrator users',
      ],
       'score' => 1,
       'index' => null,
       'matchedTranslation' => null,
       'highlight' => null,
    ]),
    2 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 13,
        'title' => 'Editors',
      ],
       'score' => 1,
       'index' => null,
       'matchedTranslation' => null,
       'highlight' => null,
    ]),
    3 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 42,
        'title' => 'Anonymous Users',
      ],
       'score' => 1,
       'index' => null,
       'matchedTranslation' => null,
       'highlight' => null,
    ]),
  ],
   'spellSuggestion' => null,
   'time' => 1,
   'timedOut' => null,
   'maxScore' => 1,
   'totalCount' => 4,
]);
