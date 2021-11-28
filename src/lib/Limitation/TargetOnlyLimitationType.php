<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as LimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;

/**
 * Limitation type which doesn't take $object into consideration while evaluation.
 *
 * @see \Ibexa\Core\Repository\Permission\PermissionCriterionResolver::getPermissionsCriterion
 */
interface TargetOnlyLimitationType extends LimitationTypeInterface
{
    /**
     * Returns criterion based on given $target for use in find() query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserReference $currentUser
     * @param array|null $targets
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getCriterionByTarget(APILimitationValue $value, APIUserReference $currentUser, ?array $targets): CriterionInterface;
}

class_alias(TargetOnlyLimitationType::class, 'eZ\Publish\Core\Limitation\TargetOnlyLimitationType');
