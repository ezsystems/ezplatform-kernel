<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

/**
 * This class represents a user to role assignment.
 *
 * @property-read \Ibexa\Contracts\Core\Repository\Values\User\User $user calls getUser()
 */
abstract class UserRoleAssignment extends RoleAssignment
{
    /**
     * Returns the user to which the role is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    abstract public function getUser(): User;
}

class_alias(UserRoleAssignment::class, 'eZ\Publish\API\Repository\Values\User\UserRoleAssignment');
