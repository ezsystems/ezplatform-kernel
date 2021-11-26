<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\FieldType\User\UserStorage\Gateway\DoctrineStorage;
use Ibexa\Core\Persistence\TransformationProcessor;

/**
 * @internal for internal use by Repository Filtering
 */
abstract class BaseUserCriterionQueryBuilder implements CriterionQueryBuilder
{
    /** @var \Ibexa\Core\Persistence\TransformationProcessor */
    private $transformationProcessor;

    public function __construct(TransformationProcessor $transformationProcessor)
    {
        $this->transformationProcessor = $transformationProcessor;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        $queryBuilder
            ->joinOnce(
                'content',
                DoctrineStorage::USER_TABLE,
                'user_storage',
                'content.id = user_storage.contentobject_id'
            );

        return null;
    }

    protected function transformCriterionValueForLikeExpression(string $value): string
    {
        return str_replace(
            '*',
            '%',
            addcslashes(
                $this->transformationProcessor->transformByGroup(
                    $value,
                    'lowercase'
                ),
                '%_'
            )
        );
    }
}

class_alias(BaseUserCriterionQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\BaseUserCriterionQueryBuilder');
