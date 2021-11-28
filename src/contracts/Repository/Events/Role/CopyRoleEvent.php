<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct;

final class CopyRoleEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $copiedRole;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $role;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct */
    private $roleCopyStruct;

    public function __construct(
        Role $copiedRole,
        Role $role,
        RoleCopyStruct $roleCopyStruct
    ) {
        $this->copiedRole = $copiedRole;
        $this->role = $role;
        $this->roleCopyStruct = $roleCopyStruct;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getCopiedRole(): Role
    {
        return $this->copiedRole;
    }

    public function getRoleCopyStruct(): RoleCopyStruct
    {
        return $this->roleCopyStruct;
    }
}

class_alias(CopyRoleEvent::class, 'eZ\Publish\API\Repository\Events\Role\CopyRoleEvent');
