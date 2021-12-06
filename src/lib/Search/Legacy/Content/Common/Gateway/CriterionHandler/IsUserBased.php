<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

class IsUserBased extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\IsUserBased;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $isUserBased = (bool)reset($criterion->value);

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select(
                'contentobject_id'
            )->from(
                'ezuser'
            );

        $queryExpression = $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );

        return $isUserBased
            ? $queryExpression
            : "NOT({$queryExpression})";
    }
}

class_alias(IsUserBased::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\IsUserBased');
