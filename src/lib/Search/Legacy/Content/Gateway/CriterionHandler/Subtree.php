<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Gateway\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

/**
 * Subtree criterion handler.
 */
class Subtree extends CriterionHandler
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
        return $criterion instanceof Criterion\Subtree;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $statements = [];
        foreach ($criterion->value as $pattern) {
            $statements[] = $queryBuilder->expr()->like(
                'path_string',
                $queryBuilder->createNamedParameter($pattern . '%', ParameterType::STRING)
            );
        }

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('contentobject_id')
            ->from('ezcontentobject_tree')
            ->where($queryBuilder->expr()->orX(...$statements));

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}

class_alias(Subtree::class, 'eZ\Publish\Core\Search\Legacy\Content\Gateway\CriterionHandler\Subtree');
