<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;

/**
 * Represents Limitation type.
 * Indicates that Limitation Type implementation properly supports $targets passed as instances of Target.
 *
 * @see \Ibexa\Contracts\Core\Limitation\Type
 * @see \Ibexa\Contracts\Core\Limitation\Target
 */
interface TargetAwareType extends Type
{
    /**
     * Evaluate ("Vote") against a main value object and targets for the context.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserReference $currentUser
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $object
     * @param \Ibexa\Contracts\Core\Limitation\Target[]|null $targets An array of location, parent or "assignment"
     *                                                                 objects, if null: none where provided by caller
     *
     * @return bool|null Returns one of ACCESS_* constants
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If value of the LimitationValue is unsupported
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If any of the arguments are invalid
     */
    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): ?bool;
}

class_alias(TargetAwareType::class, 'eZ\Publish\SPI\Limitation\TargetAwareType');
