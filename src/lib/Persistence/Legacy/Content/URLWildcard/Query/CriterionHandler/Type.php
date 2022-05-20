<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriterionHandler;

final class Type implements CriterionHandler
{
    private const FORWARD = 1;
    private const DIRECT = 2;

    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\Type;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ) {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion\Type $criterion */
        return $queryBuilder->expr()->eq(
            'type',
            $queryBuilder->createNamedParameter(
                $criterion->forward ? self::FORWARD : self::DIRECT,
                ParameterType::INTEGER,
                ':type'
            )
        );
    }
}
