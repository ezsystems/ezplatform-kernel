<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

/**
 * Parent location id criterion handler.
 */
class ParentLocationId extends CriterionHandler
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\ParentLocationId;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $subSelect = $this->connection->createQueryBuilder();
        $expr = $queryBuilder->expr();
        $subSelect
            ->select(
                'contentobject_id'
            )->from(
                'ezcontentobject_tree'
            )->where(
                $expr->in(
                    'parent_node_id',
                    $queryBuilder->createNamedParameter(
                        $criterion->value,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            );

        return $expr->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}

class_alias(ParentLocationId::class, 'eZ\Publish\Core\Search\Legacy\Content\Gateway\CriterionHandler\ParentLocationId');
