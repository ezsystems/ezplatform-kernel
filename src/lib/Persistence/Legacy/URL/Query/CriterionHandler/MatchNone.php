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

class MatchNone implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\MatchNone;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ) {
        return '1 = 0';
    }
}

class_alias(MatchNone::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchNone');
