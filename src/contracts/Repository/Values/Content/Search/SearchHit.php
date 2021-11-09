<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a SearchHit matching the query.
 */
class SearchHit extends ValueObject
{
    /**
     * The value found by the search.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\ValueObject
     */
    public $valueObject;

    /**
     * The score of this value;.
     *
     * @var float
     */
    public $score;

    /**
     * The index identifier where this value was found.
     *
     * @var string
     */
    public $index;

    /**
     * Language code of the Content translation that matched the query.
     *
     * @since 5.4.5
     *
     * @var string
     */
    public $matchedTranslation;

    /**
     * A representation of the search hit including highlighted terms.
     *
     * @var string
     */
    public $highlight;
}

class_alias(SearchHit::class, 'eZ\Publish\API\Repository\Values\Content\Search\SearchHit');
