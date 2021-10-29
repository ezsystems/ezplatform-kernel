<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class is used to create a new role.
 */
abstract class RoleCreateStruct extends ValueObject
{
    /**
     * Readable string identifier of a role.
     *
     * @var string
     */
    public $identifier;

    /**
     * Returns policies associated with the role.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct[]
     */
    abstract public function getPolicies(): iterable;

    /**
     * Adds a policy to this role.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStruct
     */
    abstract public function addPolicy(PolicyCreateStruct $policyCreateStruct): void;
}

class_alias(RoleCreateStruct::class, 'eZ\Publish\API\Repository\Values\User\RoleCreateStruct');
