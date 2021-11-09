<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

/**
 * This class represents a user group to role assignment.
 *
 * @property-read \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup calls getUserGroup()
 */
abstract class UserGroupRoleAssignment extends RoleAssignment
{
    /**
     * Returns the user group to which the role is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
     */
    abstract public function getUserGroup(): UserGroup;
}

class_alias(UserGroupRoleAssignment::class, 'eZ\Publish\API\Repository\Values\User\UserGroupRoleAssignment');
