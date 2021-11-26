<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;

final class CreateRoleEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct */
    private $roleCreateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $roleDraft;

    public function __construct(
        RoleDraft $roleDraft,
        RoleCreateStruct $roleCreateStruct
    ) {
        $this->roleCreateStruct = $roleCreateStruct;
        $this->roleDraft = $roleDraft;
    }

    public function getRoleCreateStruct(): RoleCreateStruct
    {
        return $this->roleCreateStruct;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }
}

class_alias(CreateRoleEvent::class, 'eZ\Publish\API\Repository\Events\Role\CreateRoleEvent');
