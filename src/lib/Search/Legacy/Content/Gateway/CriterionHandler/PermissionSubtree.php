<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Gateway\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Content\Location\Gateway as LocationGateway;
use Ibexa\Core\Repository\Values\Content\Query\Criterion\PermissionSubtree as PermissionSubtreeCriterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

/**
 * PermissionSubtree criterion handler.
 */
class PermissionSubtree extends CriterionHandler
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
        return $criterion instanceof PermissionSubtreeCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $table = 'permission_subtree';

        $statements = [];
        foreach ($criterion->value as $pattern) {
            $statements[] = $queryBuilder->expr()->like(
                "{$table}.path_string",
                $queryBuilder->createNamedParameter($pattern . '%')
            );
        }

        $locationTableAlias = $this->connection->quoteIdentifier($table);
        if (!$this->hasJoinedTableAs($queryBuilder, $locationTableAlias)) {
            $queryBuilder
                ->leftJoin(
                    'c',
                    LocationGateway::CONTENT_TREE_TABLE,
                    $locationTableAlias,
                    $queryBuilder->expr()->eq(
                        "{$locationTableAlias}.contentobject_id",
                        'c.id'
                    )
                );
        }

        return $queryBuilder->expr()->orX(...$statements);
    }
}

class_alias(PermissionSubtree::class, 'eZ\Publish\Core\Search\Legacy\Content\Gateway\CriterionHandler\PermissionSubtree');
