<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

class SectionIdentifier extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\SectionIdentifier;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $subSelect = $this->connection->createQueryBuilder();
        $value = (array)$criterion->value;
        $subSelect
            ->select('t1.id')
            ->from('ezcontentobject', 't1')
            ->leftJoin(
                't1',
                'ezsection',
                't2',
                't1.section_id = t2.id'
            )
            ->where(
                $queryBuilder->expr()->in(
                    't2.identifier',
                    $queryBuilder->createNamedParameter($value, Connection::PARAM_STR_ARRAY)
                )
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}

class_alias(SectionIdentifier::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\SectionIdentifier');
