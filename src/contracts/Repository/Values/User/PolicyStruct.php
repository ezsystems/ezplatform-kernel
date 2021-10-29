<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

abstract class PolicyStruct extends ValueObject
{
    /**
     * Returns list of limitations added to policy.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    abstract public function getLimitations(): iterable;

    /**
     * Adds a limitation with the given identifier and list of values.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     */
    abstract public function addLimitation(Limitation $limitation): void;
}

class_alias(PolicyStruct::class, 'eZ\Publish\API\Repository\Values\User\PolicyStruct');
