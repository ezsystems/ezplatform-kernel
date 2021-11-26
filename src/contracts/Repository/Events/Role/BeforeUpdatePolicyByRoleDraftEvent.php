<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;
use UnexpectedValueException;

final class BeforeUpdatePolicyByRoleDraftEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $roleDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft */
    private $policy;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct */
    private $policyUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft|null */
    private $updatedPolicyDraft;

    public function __construct(RoleDraft $roleDraft, PolicyDraft $policy, PolicyUpdateStruct $policyUpdateStruct)
    {
        $this->roleDraft = $roleDraft;
        $this->policy = $policy;
        $this->policyUpdateStruct = $policyUpdateStruct;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }

    public function getPolicy(): PolicyDraft
    {
        return $this->policy;
    }

    public function getPolicyUpdateStruct(): PolicyUpdateStruct
    {
        return $this->policyUpdateStruct;
    }

    public function getUpdatedPolicyDraft(): PolicyDraft
    {
        if (!$this->hasUpdatedPolicyDraft()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedPolicyDraft() or set it using setUpdatedPolicyDraft() before you call the getter.', PolicyDraft::class));
        }

        return $this->updatedPolicyDraft;
    }

    public function setUpdatedPolicyDraft(?PolicyDraft $updatedPolicyDraft): void
    {
        $this->updatedPolicyDraft = $updatedPolicyDraft;
    }

    public function hasUpdatedPolicyDraft(): bool
    {
        return $this->updatedPolicyDraft instanceof PolicyDraft;
    }
}

class_alias(BeforeUpdatePolicyByRoleDraftEvent::class, 'eZ\Publish\API\Repository\Events\Role\BeforeUpdatePolicyByRoleDraftEvent');
