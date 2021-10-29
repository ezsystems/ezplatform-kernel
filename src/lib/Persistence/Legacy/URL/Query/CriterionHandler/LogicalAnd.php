<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler;

class LogicalAnd implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\LogicalAnd;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function handle(CriteriaConverter $converter, QueryBuilder $queryBuilder, Criterion $criterion)
    {
        $subexpressions = [];
        foreach ($criterion->criteria as $subCriterion) {
            $subexpressions[] = $converter->convertCriteria($queryBuilder, $subCriterion);
        }

        return $queryBuilder->expr()->andX(...$subexpressions);
    }
}

class_alias(LogicalAnd::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\LogicalAnd');
