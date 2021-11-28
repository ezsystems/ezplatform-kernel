<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;

final class AddPolicyByRoleDraftEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $roleDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct */
    private $policyCreateStruct;

    private $updatedRoleDraft;

    public function __construct(
        RoleDraft $updatedRoleDraft,
        RoleDraft $roleDraft,
        PolicyCreateStruct $policyCreateStruct
    ) {
        $this->roleDraft = $roleDraft;
        $this->policyCreateStruct = $policyCreateStruct;
        $this->updatedRoleDraft = $updatedRoleDraft;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }

    public function getPolicyCreateStruct(): PolicyCreateStruct
    {
        return $this->policyCreateStruct;
    }

    public function getUpdatedRoleDraft(): RoleDraft
    {
        return $this->updatedRoleDraft;
    }
}

class_alias(AddPolicyByRoleDraftEvent::class, 'eZ\Publish\API\Repository\Events\Role\AddPolicyByRoleDraftEvent');
