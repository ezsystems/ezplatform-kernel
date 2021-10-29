<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\URL\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;

interface CriterionHandler
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion);

    /**
     * Generate query expression for a Criterion this handler accepts.
     *
     * accept() must be called before calling this method.
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    );
}

class_alias(CriterionHandler::class, 'eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler');
