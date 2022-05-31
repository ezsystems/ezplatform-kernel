<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Query\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\Content\UrlWildcard\Query\CriterionHandler;

final class DestinationUrl implements CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\DestinationUrl;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion
    ) {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion\DestinationUrl $criterion */
        return $queryBuilder->expr()->like(
            'destination_url',
            $queryBuilder->createNamedParameter(
                '%' . $criterion->destinationUrl . '%',
                ParameterType::STRING,
                ':destination_url'
            )
        );
    }
}
