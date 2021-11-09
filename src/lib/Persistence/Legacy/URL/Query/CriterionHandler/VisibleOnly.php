<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Content\Location\Gateway;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;

class VisibleOnly extends Base
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\VisibleOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ) {
        $this->joinContentObjectLink($queryBuilder);
        $this->joinContentObjectAttribute($queryBuilder);

        $queryBuilder->innerJoin(
            'f_def',
            Gateway::CONTENT_TREE_TABLE,
            't',
            $queryBuilder->expr()->andX(
                't.contentobject_id = f_def.contentobject_id',
                't.contentobject_version = f_def.version'
            )
        );

        return $queryBuilder->expr()->eq(
            't.is_invisible',
            $queryBuilder->createNamedParameter(0, ParameterType::INTEGER, ':location_is_invisible')
        );
    }
}

class_alias(VisibleOnly::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\VisibleOnly');
