<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;
use UnexpectedValueException;

final class BeforeCreateRoleDraftEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $role;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft|null */
    private $roleDraft;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getRoleDraft(): RoleDraft
    {
        if (!$this->hasRoleDraft()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasRoleDraft() or set it using setRoleDraft() before you call the getter.', RoleDraft::class));
        }

        return $this->roleDraft;
    }

    public function setRoleDraft(?RoleDraft $roleDraft): void
    {
        $this->roleDraft = $roleDraft;
    }

    public function hasRoleDraft(): bool
    {
        return $this->roleDraft instanceof RoleDraft;
    }
}

class_alias(BeforeCreateRoleDraftEvent::class, 'eZ\Publish\API\Repository\Events\Role\BeforeCreateRoleDraftEvent');
