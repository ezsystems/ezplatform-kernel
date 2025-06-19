<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Traits\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Limited Count count trait. Used to allow for proper limiting of count queries
 * when using Doctrine DBAL QueryBuilder.
 */
trait LimitedCountQueryTrait
{
    /**
     * Takes a QueryBuilder and wraps it in a count query. 
     * This performs the following transformation to the passed query
     * SELECT DISTINCT COUNT(DISTINCT someField) FROM XXX WHERE YYY;
     * To
     * SELECT COUNT(*) FROM (SELECT DISTINCT someField FROM XXX WHERE YYY LIMIT N) AS csub;
     * 
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $countableField
     * @param mixed $limit
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function wrapCountQuery(
        QueryBuilder $queryBuilder,
        string $countableField,
        ?int $limit,
    ): QueryBuilder {
        $useLimit = $limit !== null && $limit > 0;

        if(!$useLimit) {
            return $queryBuilder;
        }

        $querySql = $queryBuilder->select($countableField)
            ->setMaxResults($limit)
            ->getSQL();

        $countQuery = $this->connection->createQueryBuilder();
        return $countQuery
            ->select(
                $queryBuilder->getConnection()->getDatabasePlatform()->getCountExpression('*')
            )
            ->from('(' . $querySql . ')', 'csub')
            ->setParameters($queryBuilder->getParameters(), $queryBuilder->getParameterTypes());
    }
}
