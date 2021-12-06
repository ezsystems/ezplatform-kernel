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
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

class UserEmail extends CriterionHandler
{
    /** @var \Ibexa\Core\Persistence\TransformationProcessor */
    private $transformationProcessor;

    public function __construct(
        Connection $connection,
        TransformationProcessor $transformationProcessor
    ) {
        parent::__construct($connection);

        $this->transformationProcessor = $transformationProcessor;
    }

    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\UserEmail;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        if (Criterion\Operator::LIKE === $criterion->operator) {
            $expression = $queryBuilder->expr()->like(
                't1.email',
                $queryBuilder->createNamedParameter(
                    str_replace(
                        '*',
                        '%',
                        addcslashes(
                            $this->transformationProcessor->transformByGroup(
                                $criterion->value,
                                'lowercase'
                            ),
                            '%_'
                        )
                    )
                )
            );
        } else {
            $value = (array)$criterion->value;
            $expression = $queryBuilder->expr()->in(
                't1.email',
                $queryBuilder->createNamedParameter($value, Connection::PARAM_STR_ARRAY)
            );
        }

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('t1.contentobject_id')
            ->from('ezuser', 't1')
            ->where($expression);

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}

class_alias(UserEmail::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\UserEmail');
