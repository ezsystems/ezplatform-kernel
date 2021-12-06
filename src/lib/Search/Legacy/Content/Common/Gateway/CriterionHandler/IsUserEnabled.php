<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\FieldType\User\UserStorage\Gateway\DoctrineStorage as UserGateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

class IsUserEnabled extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\IsUserEnabled;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('t1.contentobject_id')
            ->from(UserGateway::USER_TABLE, 't1')
            ->leftJoin(
                't1',
                'ezuser_setting',
                't2',
                't1.contentobject_id = t2.user_id'
            )
            ->where(
                $queryBuilder->expr()->eq(
                    't2.is_enabled',
                    $queryBuilder->createNamedParameter((int)reset($criterion->value))
                )
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}

class_alias(IsUserEnabled::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\IsUserEnabled');
