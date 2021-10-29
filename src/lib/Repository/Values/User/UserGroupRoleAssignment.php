<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation as APIRoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role as APIRole;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup as APIUserGroup;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroupRoleAssignment as APIUserGroupRoleAssignment;

/**
 * This class represents a user group to role assignment.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class UserGroupRoleAssignment extends APIUserGroupRoleAssignment
{
    /**
     * the limitation of this role assignment.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null
     */
    protected $limitation;

    /**
     * the role which is assigned to the user group.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Role
     */
    protected $role;

    /**
     * user group to which the role is assigned to.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
     */
    protected $userGroup;

    /**
     * Returns the limitation of the role assignment.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null
     */
    public function getRoleLimitation(): ?APIRoleLimitation
    {
        return $this->limitation;
    }

    /**
     * Returns the role to which the user group is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role
     */
    public function getRole(): APIRole
    {
        return $this->role;
    }

    /**
     * Returns the user group to which the role is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
     */
    public function getUserGroup(): APIUserGroup
    {
        return $this->userGroup;
    }
}

class_alias(UserGroupRoleAssignment::class, 'eZ\Publish\Core\Repository\Values\User\UserGroupRoleAssignment');
