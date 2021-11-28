<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * Content Type Identifier Criterion visitor query builder.
 *
 * @see \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier
 *
 * @internal for internal use by Repository Filtering
 */
final class IdentifierQueryBuilder extends BaseQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof ContentTypeIdentifier;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        return $queryBuilder->expr()->in(
            'content_type.identifier',
            $queryBuilder->createNamedParameter(
                $criterion->value,
                Connection::PARAM_STR_ARRAY
            )
        );
    }
}

class_alias(IdentifierQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\IdentifierQueryBuilder');
