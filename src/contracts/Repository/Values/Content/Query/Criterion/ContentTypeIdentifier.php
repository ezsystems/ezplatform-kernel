<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * A criterion that matches content based on its ContentType Identifier.
 *
 * Supported operators:
 * - IN: will match from a list of ContentTypeIdentifier
 * - EQ: will match against one ContentTypeIdentifier
 */
class ContentTypeIdentifier extends Criterion implements FilteringCriterion
{
    /**
     * Creates a new ContentType criterion.
     *
     * Content will be matched if it matches one of the contentTypeIdentifier in $value
     *
     * @param string|string[] $value One or more content type identifiers that must be matched
     *
     * @throws \InvalidArgumentException if the value type doesn't match the operator
     */
    public function __construct($value)
    {
        parent::__construct(null, null, $value);
    }

    public function getSpecifications(): array
    {
        return [
            new Specifications(
                Operator::IN,
                Specifications::FORMAT_ARRAY,
                Specifications::TYPE_STRING
            ),
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE,
                Specifications::TYPE_STRING
            ),
        ];
    }
}

class_alias(ContentTypeIdentifier::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier');
