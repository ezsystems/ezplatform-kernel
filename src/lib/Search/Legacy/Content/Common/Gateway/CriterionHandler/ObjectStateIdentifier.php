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

class ObjectStateIdentifier extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\ObjectStateIdentifier;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $value = (array)$criterion->value;
        $matchStateIdentifier = $queryBuilder->expr()->in(
            't2.identifier',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_STR_ARRAY)
        );

        if (null !== $criterion->target) {
            $criterionTarget = (array)$criterion->target;
            $constraints = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->in(
                    't3.identifier',
                    $queryBuilder->createNamedParameter(
                        $criterionTarget,
                        Connection::PARAM_STR_ARRAY
                    )
                ),
                $matchStateIdentifier
            );
        } else {
            $constraints = $matchStateIdentifier;
        }

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('t1.contentobject_id')
            ->from('ezcobj_state_link', 't1')
            ->leftJoin(
                't1',
                'ezcobj_state',
                't2',
                't1.contentobject_state_id = t2.id',
            )
            ->leftJoin(
                't2',
                'ezcobj_state_group',
                't3',
                't2.group_id = t3.id'
            )
            ->where($constraints);

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}

class_alias(ObjectStateIdentifier::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\ObjectStateIdentifier');
