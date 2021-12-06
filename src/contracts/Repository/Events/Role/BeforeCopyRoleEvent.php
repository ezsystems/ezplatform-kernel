<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct;
use UnexpectedValueException;

final class BeforeCopyRoleEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role */
    private $role;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct */
    private $roleCopyStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role|null */
    private $copiedRole;

    public function __construct(Role $role, RoleCopyStruct $roleCopyStruct)
    {
        $this->role = $role;
        $this->roleCopyStruct = $roleCopyStruct;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getRoleCopyStruct(): RoleCopyStruct
    {
        return $this->roleCopyStruct;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function getCopiedRole(): Role
    {
        if (!$this->hasCopiedRole()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasCopiedRole() or set it using setCopiedRole() before you call the getter.', Role::class));
        }

        return $this->copiedRole;
    }

    public function setCopiedRole(?Role $copiedRole): void
    {
        $this->copiedRole = $copiedRole;
    }

    public function hasCopiedRole(): bool
    {
        return $this->copiedRole instanceof Role;
    }
}

class_alias(BeforeCopyRoleEvent::class, 'eZ\Publish\API\Repository\Events\Role\BeforeCopyRoleEvent');
