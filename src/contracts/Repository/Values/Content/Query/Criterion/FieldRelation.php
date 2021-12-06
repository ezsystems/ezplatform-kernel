<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * A criterion that matches Content based on the relations in relation field.
 * This includes Relation and RelationList field types in standard installation, but also any
 * other field type storing {@link \Ibexa\Contracts\Core\Repository\Values\Content\Relation::FIELD}
 * type relation.
 *
 * Supported operators:
 * - IN: will match if Content relates to one or more of the given ids through given relation field
 * - CONTAINS: will match if Content relates to all of the given ids through given relation field
 */
class FieldRelation extends Criterion
{
    public function getSpecifications(): array
    {
        $types = Specifications::TYPE_INTEGER | Specifications::TYPE_STRING;

        return [
            new Specifications(Operator::CONTAINS, Specifications::FORMAT_SINGLE | Specifications::FORMAT_ARRAY, $types),
            new Specifications(Operator::IN, Specifications::FORMAT_ARRAY, $types),
        ];
    }
}

class_alias(FieldRelation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\FieldRelation');
