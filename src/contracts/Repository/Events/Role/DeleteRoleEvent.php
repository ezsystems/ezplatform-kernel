<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Role;

final class DeleteRoleEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $role;

    public function __construct(
        Role $role
    ) {
        $this->role = $role;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}

class_alias(DeleteRoleEvent::class, 'eZ\Publish\API\Repository\Events\Role\DeleteRoleEvent');
