<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Legacy\Content\Location\Gateway\CriterionHandler\Location;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use RuntimeException;

/**
 * Location priority criterion handler.
 */
class Priority extends CriterionHandler
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\Location\Priority;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $column = 'priority';

        switch ($criterion->operator) {
            case Criterion\Operator::BETWEEN:
                return $this->dbPlatform->getBetweenExpression(
                    $column,
                    $queryBuilder->createNamedParameter($criterion->value[0], ParameterType::STRING),
                    $queryBuilder->createNamedParameter($criterion->value[1], ParameterType::STRING)
                );

            case Criterion\Operator::GT:
            case Criterion\Operator::GTE:
            case Criterion\Operator::LT:
            case Criterion\Operator::LTE:
                $operatorFunction = $this->comparatorMap[$criterion->operator];

                return $queryBuilder->expr()->$operatorFunction(
                    $column,
                    $queryBuilder->createNamedParameter(reset($criterion->value), ParameterType::STRING)
                );

            default:
                throw new RuntimeException(
                    "Unknown operator '{$criterion->operator}' for Priority Criterion handler."
                );
        }
    }
}
