<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct as APIPolicyCreateStruct;

/**
 * This class is used to create a policy.
 *
 * @internal Meant for internal use by Repository, type hint against API instead.
 */
class PolicyCreateStruct extends APIPolicyCreateStruct
{
    /**
     * List of limitations added to policy.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    protected $limitations = [];

    /**
     * Returns list of limitations added to policy.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    public function getLimitations(): iterable
    {
        return $this->limitations;
    }

    /**
     * Adds a limitation with the given identifier and list of values.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     */
    public function addLimitation(Limitation $limitation): void
    {
        $limitationIdentifier = $limitation->getIdentifier();
        $this->limitations[$limitationIdentifier] = $limitation;
    }
}

class_alias(PolicyCreateStruct::class, 'eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct');
