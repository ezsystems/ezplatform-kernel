<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Section;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\SectionIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Persistence\Legacy\Content\Section\Gateway;

/**
 * Section Identifier Filtering Criterion Query Builder.
 *
 * @internal for internal use by Repository Filtering
 */
final class IdentifierQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof SectionIdentifier;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        $queryBuilder
            ->joinOnce(
                'content',
                Gateway::CONTENT_SECTION_TABLE,
                'section',
                'content.section_id = section.id'
            );

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\SectionIdentifier $criterion */
        return $queryBuilder->expr()->in(
            'section.identifier',
            $queryBuilder->createNamedParameter(
                (array)$criterion->value,
                Connection::PARAM_STR_ARRAY
            )
        );
    }
}

class_alias(IdentifierQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Section\IdentifierQueryBuilder');
