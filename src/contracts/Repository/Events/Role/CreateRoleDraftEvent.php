<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;

final class CreateRoleDraftEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $role;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $roleDraft;

    public function __construct(
        RoleDraft $roleDraft,
        Role $role
    ) {
        $this->role = $role;
        $this->roleDraft = $roleDraft;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }
}

class_alias(CreateRoleDraftEvent::class, 'eZ\Publish\API\Repository\Events\Role\CreateRoleDraftEvent');
