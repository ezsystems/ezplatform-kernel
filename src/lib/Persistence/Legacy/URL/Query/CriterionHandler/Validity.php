<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler;

class Validity implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\Validity;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ) {
        /** @var \Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion\Validity $criterion */
        return $queryBuilder->expr()->eq(
            'is_valid',
            $queryBuilder->createNamedParameter(
                (int)$criterion->isValid,
                ParameterType::INTEGER,
                ':is_valid'
            )
        );
    }
}

class_alias(Validity::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\Validity');
