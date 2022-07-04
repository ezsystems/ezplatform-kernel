<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator as CriterionOperator;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Persistence\TransformationProcessor;
use RuntimeException;

/**
 * Content locator gateway implementation using the DoctrineDatabase.
 */
abstract class Handler
{
    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /**
     * Map of criterion operators to the respective function names
     * in the DoctrineDatabase DBAL.
     *
     * @var array
     */
    protected $comparatorMap = [
        CriterionOperator::EQ => 'eq',
        CriterionOperator::GT => 'gt',
        CriterionOperator::GTE => 'gte',
        CriterionOperator::LT => 'lt',
        CriterionOperator::LTE => 'lte',
    ];

    /**
     * Transformation processor.
     *
     * @var \eZ\Publish\Core\Persistence\TransformationProcessor
     */
    protected $transformationProcessor;

    /** @var \Doctrine\DBAL\Platforms\AbstractPlatform|null */
    protected $dbPlatform;

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(Connection $connection, TransformationProcessor $transformationProcessor)
    {
        $this->connection = $connection;
        $this->dbPlatform = $connection->getDatabasePlatform();
        $this->transformationProcessor = $transformationProcessor;
    }

    /**
     * Generates query expression for operator and value of a Field Criterion.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $outerQuery to be used only for parameter binding
     * @param \Doctrine\DBAL\Query\QueryBuilder $subQuery to modify Field Value query constraints
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     *
     * @throws \eZ\Publish\API\Exception\InvalidArgumentException If passed more than 1 argument to unary operator.
     * @throws \RuntimeException If operator is not handled.
     */
    public function handle(
        QueryBuilder $outerQuery,
        QueryBuilder $subQuery,
        Criterion $criterion,
        string $column
    ) {
        if (is_array($criterion->value) && $this->isOperatorUnary($criterion->operator)) {
            if (count($criterion->value) > 1) {
                throw new InvalidArgumentException('$criterion->value', "Too many arguments for unary operator '$criterion->operator'");
            }

            $criterion->value = reset($criterion->value);
        }

        switch ($criterion->operator) {
            case Criterion\Operator::IN:
                $values = array_map([$this, 'prepareParameter'], $criterion->value);
                $filter = $subQuery->expr()->in(
                    $column,
                    $outerQuery->createNamedParameter(
                        $values,
                        $this->getParamArrayType($values)
                    )
                );
                break;

            case Criterion\Operator::BETWEEN:
                $filter = $this->dbPlatform->getBetweenExpression(
                    $column,
                    $outerQuery->createNamedParameter($this->lowerCase($criterion->value[0])),
                    $outerQuery->createNamedParameter($this->lowerCase($criterion->value[1]))
                );
                break;

            case Criterion\Operator::EQ:
            case Criterion\Operator::GT:
            case Criterion\Operator::GTE:
            case Criterion\Operator::LT:
            case Criterion\Operator::LTE:
                $operatorFunction = $this->comparatorMap[$criterion->operator];
                $filter = $subQuery->expr()->{$operatorFunction}(
                    $column,
                    $this->createNamedParameter($outerQuery, $column, $criterion->value)
                );
                break;

            case Criterion\Operator::LIKE:
                $value = str_replace('*', '%', $this->prepareLikeString($criterion->value));

                $filter = $subQuery->expr()->like(
                    $column,
                    $outerQuery->createNamedParameter($value)
                );
                break;

            case Criterion\Operator::CONTAINS:
                $filter = $subQuery->expr()->like(
                    $column,
                    $outerQuery->createNamedParameter(
                        '%' . $this->prepareLikeString($criterion->value) . '%'
                    )
                );
                break;

            default:
                throw new RuntimeException(
                    "Unknown operator '{$criterion->operator}' for Field Criterion handler."
                );
        }

        return $filter;
    }

    /**
     * Returns the given $string prepared for use in SQL LIKE clause.
     *
     * LIKE clause wildcards '%' and '_' contained in the given $string will be escaped.
     */
    protected function prepareLikeString(string $string): string
    {
        return addcslashes($this->lowerCase($string), '%_');
    }

    /**
     * Downcases a given string using string transformation processor.
     */
    protected function lowerCase(string $string): string
    {
        return $this->transformationProcessor->transformByGroup($string, 'lowercase');
    }

    /**
     * @param scalar|array<scalar> $value
     *
     * @return scalar|array<scalar>
     */
    private function prepareParameter($value)
    {
        if (is_string($value)) {
            return $this->lowerCase($value);
        } elseif (is_array($value)) {
            return array_map([$this, 'prepareParameter'], $value);
        }

        return $value;
    }

    private function createNamedParameter(QueryBuilder $outerQuery, string $column, $value): ?string
    {
        switch ($column) {
            case 'sort_key_string':
                $parameterValue = $this->prepareParameter($value);
                $parameterType = ParameterType::STRING;
                break;
            case 'sort_key_integer':
                $parameterValue = (int)$value;
                $parameterType = ParameterType::INTEGER;
                break;
            default:
                $parameterValue = $value;
                $parameterType = null;
        }

        return $outerQuery->createNamedParameter(
            $parameterValue,
            $parameterType
        );
    }

    private function isOperatorUnary(string $operator): bool
    {
        return !in_array($operator, [
            Criterion\Operator::IN,
            Criterion\Operator::BETWEEN,
        ], true);
    }

    /**
     * @param array<int, scalar> $values
     *
     * @throws \eZ\Publish\API\Exception\InvalidArgumentException If value contain unhandled or more than one parameter type
     */
    private function getParamArrayType(array $values): int
    {
        $types = array_unique(array_map('gettype', $values));

        if (count($types) > 1) {
            throw new InvalidArgumentException('$values', 'Cannot mix parameter types: ' . implode(', ', $types));
        }

        switch ($types[0]) {
            case 'integer':
            case 'double':
            case 'boolean':
                return Connection::PARAM_INT_ARRAY;
            case 'string':
                return Connection::PARAM_STR_ARRAY;
        }

        throw new InvalidArgumentException('$values', 'Unhandled parameter type: ' . $types[0]);
    }
}
