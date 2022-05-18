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

final class SourceUrl implements CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\SourceUrl;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ) {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion\SourceUrl $criterion */
        return $queryBuilder->expr()->like(
            'source_url',
            $queryBuilder->createNamedParameter(
                '%' . $criterion->sourceUrl . '%',
                ParameterType::STRING,
                ':source_url'
            )
        );
    }
}
