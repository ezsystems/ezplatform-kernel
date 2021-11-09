<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation as APIRoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role as APIRole;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Contracts\Core\Repository\Values\User\UserRoleAssignment as APIUserRoleAssignment;

/**
 * This class represents a user to role assignment.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class UserRoleAssignment extends APIUserRoleAssignment
{
    /**
     * the limitation of this role assignment.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null
     */
    protected $limitation;

    /**
     * the role which is assigned to the user.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Role
     */
    protected $role;

    /**
     * user to which the role is assigned to.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    protected $user;

    /**
     * Returns the limitation of the user role assignment.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null
     */
    public function getRoleLimitation(): ?APIRoleLimitation
    {
        return $this->limitation;
    }

    /**
     * Returns the role to which the user is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role
     */
    public function getRole(): APIRole
    {
        return $this->role;
    }

    /**
     * Returns the user to which the role is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    public function getUser(): APIUser
    {
        return $this->user;
    }
}

class_alias(UserRoleAssignment::class, 'eZ\Publish\Core\Repository\Values\User\UserRoleAssignment');
