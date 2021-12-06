<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Persistence\Legacy\Content\Type\Gateway as ContentTypeGateway;

/**
 * Content Type Criterion visitor query builder base.
 *
 * @internal for internal use by Repository Filtering
 */
abstract class BaseQueryBuilder implements CriterionQueryBuilder
{
    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        $queryBuilder
            ->joinOnce(
                'content',
                ContentTypeGateway::CONTENT_TYPE_TABLE,
                'content_type',
                'content.contentclass_id = content_type.id AND content_type.version = 0'
            );

        // the returned query constraint depends on concrete implementations
        return null;
    }
}

class_alias(BaseQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\BaseQueryBuilder');
