<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct as APIPolicyCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct as APIRoleCopyStruct;

/**
 * This class is used to create a new role.
 *
 * @internal Meant for internal use by Repository, type hint against API instead.
 */
class RoleCopyStruct extends APIRoleCopyStruct
{
    /**
     * Policies associated with the role.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct[]
     */
    protected $policies = [];

    /**
     * Returns policies associated with the role.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct[]
     */
    public function getPolicies(): iterable
    {
        return $this->policies;
    }

    /**
     * Adds a policy to this role.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStruct
     */
    public function addPolicy(APIPolicyCreateStruct $policyCreateStruct): void
    {
        $this->policies[] = $policyCreateStruct;
    }
}

class_alias(RoleCopyStruct::class, 'eZ\Publish\Core\Repository\Values\User\RoleCopyStruct');
