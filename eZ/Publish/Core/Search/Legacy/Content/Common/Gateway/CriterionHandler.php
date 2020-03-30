<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Legacy\Content\Common\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;

abstract class CriterionHandler
{
    /**
     * Map of criterion operators to the respective function names in the zeta
     * Database abstraction layer.
     *
     * @var array
     */
    protected $comparatorMap = [
        Operator::EQ => 'eq',
        Operator::GT => 'gt',
        Operator::GTE => 'gte',
        Operator::LT => 'lt',
        Operator::LTE => 'lte',
        Operator::LIKE => 'like',
    ];

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    abstract public function accept(Criterion $criterion);

    /**
     * Generate query expression for a Criterion this handler accepts.
     *
     * accept() must be called before calling this method.
     *
     * @param array $languageSettings
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotImplementedException
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     */
    abstract public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    );

    /**
     * Returns a unique table name.
     *
     * @return string
     */
    protected function getUniqueTableName()
    {
        return uniqid('CriterionHandler', true);
    }
}
