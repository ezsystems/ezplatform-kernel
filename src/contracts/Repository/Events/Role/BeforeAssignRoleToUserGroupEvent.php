<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;

final class BeforeAssignRoleToUserGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $role;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup */
    private $userGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation */
    private $roleLimitation;

    public function __construct(Role $role, UserGroup $userGroup, ?RoleLimitation $roleLimitation = null)
    {
        $this->role = $role;
        $this->userGroup = $userGroup;
        $this->roleLimitation = $roleLimitation;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    public function getRoleLimitation(): ?RoleLimitation
    {
        return $this->roleLimitation;
    }
}

class_alias(BeforeAssignRoleToUserGroupEvent::class, 'eZ\Publish\API\Repository\Events\Role\BeforeAssignRoleToUserGroupEvent');
