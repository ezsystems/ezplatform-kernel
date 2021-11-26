<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\RoleServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Role\AddPolicyByRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\AssignRoleToUserEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\AssignRoleToUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeAddPolicyByRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeAssignRoleToUserEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeAssignRoleToUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeCopyRoleEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeCreateRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeCreateRoleEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeDeleteRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeDeleteRoleEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforePublishRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeRemovePolicyByRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeRemoveRoleAssignmentEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeUpdatePolicyByRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\BeforeUpdateRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\CopyRoleEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\CreateRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\CreateRoleEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\DeleteRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\DeleteRoleEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\PublishRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\RemovePolicyByRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\RemoveRoleAssignmentEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\UpdatePolicyByRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\UpdateRoleDraftEvent;
use Ibexa\Contracts\Core\Repository\RoleService as RoleServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;
use Ibexa\Contracts\Core\Repository\Values\User\RoleUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RoleService extends RoleServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        RoleServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createRole(RoleCreateStruct $roleCreateStruct): RoleDraft
    {
        $eventData = [$roleCreateStruct];

        $beforeEvent = new BeforeCreateRoleEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getRoleDraft();
        }

        $roleDraft = $beforeEvent->hasRoleDraft()
            ? $beforeEvent->getRoleDraft()
            : $this->innerService->createRole($roleCreateStruct);

        $this->eventDispatcher->dispatch(
            new CreateRoleEvent($roleDraft, ...$eventData)
        );

        return $roleDraft;
    }

    public function createRoleDraft(Role $role): RoleDraft
    {
        $eventData = [$role];

        $beforeEvent = new BeforeCreateRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getRoleDraft();
        }

        $roleDraft = $beforeEvent->hasRoleDraft()
            ? $beforeEvent->getRoleDraft()
            : $this->innerService->createRoleDraft($role);

        $this->eventDispatcher->dispatch(
            new CreateRoleDraftEvent($roleDraft, ...$eventData)
        );

        return $roleDraft;
    }

    public function copyRole(
        Role $role,
        RoleCopyStruct $roleCopyStruct
    ): Role {
        $eventData = [
            $role,
            $roleCopyStruct,
        ];

        $beforeEvent = new BeforeCopyRoleEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getCopiedRole();
        }

        $copiedRole = $beforeEvent->hasCopiedRole()
            ? $beforeEvent->getCopiedRole()
            : $this->innerService->copyRole($role, $roleCopyStruct);

        $this->eventDispatcher->dispatch(
            new CopyRoleEvent($copiedRole, ...$eventData)
        );

        return $copiedRole;
    }

    public function updateRoleDraft(
        RoleDraft $roleDraft,
        RoleUpdateStruct $roleUpdateStruct
    ): RoleDraft {
        $eventData = [
            $roleDraft,
            $roleUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRoleDraft();
        }

        $updatedRoleDraft = $beforeEvent->hasUpdatedRoleDraft()
            ? $beforeEvent->getUpdatedRoleDraft()
            : $this->innerService->updateRoleDraft($roleDraft, $roleUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateRoleDraftEvent($updatedRoleDraft, ...$eventData)
        );

        return $updatedRoleDraft;
    }

    public function addPolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyCreateStruct $policyCreateStruct
    ): RoleDraft {
        $eventData = [
            $roleDraft,
            $policyCreateStruct,
        ];

        $beforeEvent = new BeforeAddPolicyByRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRoleDraft();
        }

        $updatedRoleDraft = $beforeEvent->hasUpdatedRoleDraft()
            ? $beforeEvent->getUpdatedRoleDraft()
            : $this->innerService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);

        $this->eventDispatcher->dispatch(
            new AddPolicyByRoleDraftEvent($updatedRoleDraft, ...$eventData)
        );

        return $updatedRoleDraft;
    }

    public function removePolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyDraft $policyDraft
    ): RoleDraft {
        $eventData = [
            $roleDraft,
            $policyDraft,
        ];

        $beforeEvent = new BeforeRemovePolicyByRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRoleDraft();
        }

        $updatedRoleDraft = $beforeEvent->hasUpdatedRoleDraft()
            ? $beforeEvent->getUpdatedRoleDraft()
            : $this->innerService->removePolicyByRoleDraft($roleDraft, $policyDraft);

        $this->eventDispatcher->dispatch(
            new RemovePolicyByRoleDraftEvent($updatedRoleDraft, ...$eventData)
        );

        return $updatedRoleDraft;
    }

    public function updatePolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyDraft $policy,
        PolicyUpdateStruct $policyUpdateStruct
    ): PolicyDraft {
        $eventData = [
            $roleDraft,
            $policy,
            $policyUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdatePolicyByRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedPolicyDraft();
        }

        $updatedPolicyDraft = $beforeEvent->hasUpdatedPolicyDraft()
            ? $beforeEvent->getUpdatedPolicyDraft()
            : $this->innerService->updatePolicyByRoleDraft($roleDraft, $policy, $policyUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdatePolicyByRoleDraftEvent($updatedPolicyDraft, ...$eventData)
        );

        return $updatedPolicyDraft;
    }

    public function deleteRoleDraft(RoleDraft $roleDraft): void
    {
        $eventData = [$roleDraft];

        $beforeEvent = new BeforeDeleteRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteRoleDraft($roleDraft);

        $this->eventDispatcher->dispatch(
            new DeleteRoleDraftEvent(...$eventData)
        );
    }

    public function publishRoleDraft(RoleDraft $roleDraft): void
    {
        $eventData = [$roleDraft];

        $beforeEvent = new BeforePublishRoleDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->publishRoleDraft($roleDraft);

        $this->eventDispatcher->dispatch(
            new PublishRoleDraftEvent(...$eventData)
        );
    }

    public function deleteRole(Role $role): void
    {
        $eventData = [$role];

        $beforeEvent = new BeforeDeleteRoleEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteRole($role);

        $this->eventDispatcher->dispatch(
            new DeleteRoleEvent(...$eventData)
        );
    }

    public function assignRoleToUserGroup(
        Role $role,
        UserGroup $userGroup,
        RoleLimitation $roleLimitation = null
    ): void {
        $eventData = [
            $role,
            $userGroup,
            $roleLimitation,
        ];

        $beforeEvent = new BeforeAssignRoleToUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignRoleToUserGroup($role, $userGroup, $roleLimitation);

        $this->eventDispatcher->dispatch(
            new AssignRoleToUserGroupEvent(...$eventData)
        );
    }

    public function assignRoleToUser(
        Role $role,
        User $user,
        RoleLimitation $roleLimitation = null
    ): void {
        $eventData = [
            $role,
            $user,
            $roleLimitation,
        ];

        $beforeEvent = new BeforeAssignRoleToUserEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignRoleToUser($role, $user, $roleLimitation);

        $this->eventDispatcher->dispatch(
            new AssignRoleToUserEvent(...$eventData)
        );
    }

    public function removeRoleAssignment(RoleAssignment $roleAssignment): void
    {
        $eventData = [$roleAssignment];

        $beforeEvent = new BeforeRemoveRoleAssignmentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->removeRoleAssignment($roleAssignment);

        $this->eventDispatcher->dispatch(
            new RemoveRoleAssignmentEvent(...$eventData)
        );
    }
}

class_alias(RoleService::class, 'eZ\Publish\Core\Event\RoleService');
