<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Location\Gateway\CriterionHandler\Location;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use RuntimeException;

/**
 * Location main status criterion handler.
 */
class IsMainLocation extends CriterionHandler
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\Location\IsMainLocation;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $idColumn = 't.node_id';
        $mainIdColumn = 't.main_node_id';

        switch ($criterion->value[0]) {
            case Criterion\Location\IsMainLocation::MAIN:
                return $queryBuilder->expr()->eq(
                    $idColumn,
                    $mainIdColumn
                );

            case Criterion\Location\IsMainLocation::NOT_MAIN:
                return $queryBuilder->expr()->neq(
                    $idColumn,
                    $mainIdColumn
                );

            default:
                throw new RuntimeException(
                    "Unknown value '{$criterion->value[0]}' for IsMainLocation Criterion handler."
                );
        }
    }
}

class_alias(IsMainLocation::class, 'eZ\Publish\Core\Search\Legacy\Content\Location\Gateway\CriterionHandler\Location\IsMainLocation');
