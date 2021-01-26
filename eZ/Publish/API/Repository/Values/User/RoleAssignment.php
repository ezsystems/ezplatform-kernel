<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\User;

use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * This value object represents an assignment od a user or user group to a role including a limitation.
 *
 * @property-read \eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation|null $limitation the limitation of this role assignment
 * @property-read \eZ\Publish\API\Repository\Values\User\Role $role the role which is assigned to the user or user group
 */
abstract class RoleAssignment extends ValueObject
{
    /**
     * The unique id of the role assignment.
     *
     * @var int
     */
    protected $id;

    /**
     * Returns the limitation of the role assignment.
     *
     * @return \eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation|null
     */
    abstract public function getRoleLimitation(): ?RoleLimitation;

    /**
     * Returns the role to which the user or user group is assigned to.
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    abstract public function getRole(): Role;
}
