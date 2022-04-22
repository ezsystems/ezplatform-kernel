<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler;

final class LogicalNot implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\LogicalNot;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotImplementedException
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ): string {
        return sprintf(
            'NOT (%s)',
            $converter->convertCriteria($queryBuilder, $criterion->criteria[0])
        );
    }
}
