<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet\SectionFacet;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

return SearchResult::__set_state([
   'facets' => [
    0 => SectionFacet::__set_state([
       'entries' => [
        1 => 2,
      ],
       'name' => 'section',
    ]),
  ],
   'searchHits' => [
    0 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 57,
        'title' => 'Home',
      ],
       'score' => 1.0,
       'index' => null,
       'highlight' => null,
    ]),
    1 => SearchHit::__set_state([
       'valueObject' => [
        'id' => 58,
        'title' => 'Contact Us',
      ],
       'score' => 1.0,
       'index' => null,
       'highlight' => null,
    ]),
  ],
   'spellSuggestion' => null,
   'time' => 1,
   'timedOut' => null,
   'maxScore' => 1.0,
   'totalCount' => 2,
]);
