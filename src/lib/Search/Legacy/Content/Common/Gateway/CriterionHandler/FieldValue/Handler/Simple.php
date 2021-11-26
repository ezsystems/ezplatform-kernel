<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler;

/**
 * Content locator gateway implementation using the DoctrineDatabase.
 *
 * Simple value handler is used for creating a filter on a value that makes sense to match on only as a whole.
 * Eg. timestamp, integer, boolean, relation Content id
 */
class Simple extends Handler
{
    public function handle(
        QueryBuilder $outerQuery,
        QueryBuilder $subQuery,
        Criterion $criterion,
        string $column
    ) {
        // For "Simple" FieldTypes, handle the following as equal:
        // - Contains
        // - LIKE when against int column
        if (
            $criterion->operator === Criterion\Operator::CONTAINS ||
            ($criterion->operator === Criterion\Operator::LIKE && $column === 'sort_key_int')
        ) {
            $filter = $subQuery->expr()->eq(
                $column,
                $outerQuery->createNamedParameter($this->lowerCase($criterion->value))
            );
        } else {
            $filter = parent::handle($outerQuery, $subQuery, $criterion, $column);
        }

        return $filter;
    }
}

class_alias(Simple::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler\Simple');
