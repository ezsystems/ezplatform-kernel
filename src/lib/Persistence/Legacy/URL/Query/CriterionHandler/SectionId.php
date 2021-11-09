<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;

class SectionId extends Base
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\SectionId;
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
        $this->joinContentObject($queryBuilder);

        return $queryBuilder->expr()->in(
            'c.section_id',
            $queryBuilder->createNamedParameter(
                $criterion->sectionIds,
                Connection::PARAM_INT_ARRAY,
                ':section_ids'
            )
        );
    }
}

class_alias(SectionId::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\SectionId');
