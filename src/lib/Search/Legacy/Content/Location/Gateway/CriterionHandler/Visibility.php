<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Location\Gateway\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use RuntimeException;

/**
 * Location visibility criterion handler.
 */
class Visibility extends CriterionHandler
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
        return $criterion instanceof Criterion\Visibility;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $column = 't.is_invisible';

        switch ($criterion->value[0]) {
            case Criterion\Visibility::VISIBLE:
                return $queryBuilder->expr()->eq(
                    $column,
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                );

            case Criterion\Visibility::HIDDEN:
                return $queryBuilder->expr()->eq(
                    $column,
                    $queryBuilder->createNamedParameter(1, ParameterType::INTEGER)
                );

            default:
                throw new RuntimeException(
                    "Unknown value '{$criterion->value[0]}' for Visibility Criterion handler."
                );
        }
    }
}

class_alias(Visibility::class, 'eZ\Publish\Core\Search\Legacy\Content\Location\Gateway\CriterionHandler\Visibility');
