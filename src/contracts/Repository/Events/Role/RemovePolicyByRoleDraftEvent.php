<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;

final class RemovePolicyByRoleDraftEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $roleDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft */
    private $policyDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $updatedRoleDraft;

    public function __construct(
        RoleDraft $updatedRoleDraft,
        RoleDraft $roleDraft,
        PolicyDraft $policyDraft
    ) {
        $this->roleDraft = $roleDraft;
        $this->policyDraft = $policyDraft;
        $this->updatedRoleDraft = $updatedRoleDraft;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }

    public function getPolicyDraft(): PolicyDraft
    {
        return $this->policyDraft;
    }

    public function getUpdatedRoleDraft(): RoleDraft
    {
        return $this->updatedRoleDraft;
    }
}

class_alias(RemovePolicyByRoleDraftEvent::class, 'eZ\Publish\API\Repository\Events\Role\RemovePolicyByRoleDraftEvent');
