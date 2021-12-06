<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace  Ibexa\Contracts\Core\Repository;

use Ibexa\Contracts\Core\Limitation\Type;
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

/**
 * This service provides methods for managing Roles and Policies.
 */
interface RoleService
{
    /**
     * Creates a new RoleDraft.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct $roleCreateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to create a role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the name of the role already exists or if limitation of the same type
     *         is repeated in the policy create struct or if limitation is not allowed on module/function
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if a policy limitation in the $roleCreateStruct is not valid
     */
    public function createRole(RoleCreateStruct $roleCreateStruct): RoleDraft;

    /**
     * Creates a new RoleDraft for existing Role.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to create a role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the Role already has a Role Draft that will need to be removed first
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if a policy limitation in the $roleCreateStruct is not valid
     */
    public function createRoleDraft(Role $role): RoleDraft;

    /**
     * Copies an existing Role.
     *
     * @since eZ Platform 3.0
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to copy a role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the name of the role already exists or if limitation of the same type
     *         is repeated in the policy create struct or if limitation is not allowed on module/function
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if a policy limitation in the $roleCopyStruct is not valid
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException if CopyRoleEvent does not posses valid Role object
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if newly cloned Role does not exist
     */
    public function copyRole(Role $role, RoleCopyStruct $roleCopyStruct): Role;

    /**
     * Loads a RoleDraft for the given id.
     *
     * @since 6.0
     *
     * @param int $id
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to read this role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if a RoleDraft with the given id was not found
     */
    public function loadRoleDraft(int $id): RoleDraft;

    /**
     * Loads a RoleDraft by the ID of the role it was created from.
     *
     * @param int $roleId ID of the role the draft was created from.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to read this role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if a RoleDraft with the given id was not found
     */
    public function loadRoleDraftByRoleId(int $roleId): RoleDraft;

    /**
     * Updates the properties of a RoleDraft.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleUpdateStruct $roleUpdateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to update a role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the identifier of the RoleDraft already exists
     */
    public function updateRoleDraft(RoleDraft $roleDraft, RoleUpdateStruct $roleUpdateStruct): RoleDraft;

    /**
     * Adds a new policy to the RoleDraft.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to add  a policy
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if limitation of the same type is repeated in policy create
     *                                                                        struct or if limitation is not allowed on module/function
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if a limitation in the $policyCreateStruct is not valid
     */
    public function addPolicyByRoleDraft(RoleDraft $roleDraft, PolicyCreateStruct $policyCreateStruct): RoleDraft;

    /**
     * Removes a policy from a RoleDraft.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policyDraft the policy to remove from the RoleDraft
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to remove a policy
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if policy does not belong to the given RoleDraft
     */
    public function removePolicyByRoleDraft(RoleDraft $roleDraft, PolicyDraft $policyDraft): RoleDraft;

    /**
     * Updates the limitations of a policy. The module and function cannot be changed and
     * the limitations are replaced by the ones in $roleUpdateStruct.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct $policyUpdateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to update a policy
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if limitation of the same type is repeated in policy update
     *                                                                        struct or if limitation is not allowed on module/function
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if a limitation in the $policyUpdateStruct is not valid
     */
    public function updatePolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyDraft $policy,
        PolicyUpdateStruct $policyUpdateStruct
    ): PolicyDraft;

    /**
     * Deletes the given RoleDraft.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to delete this RoleDraft
     */
    public function deleteRoleDraft(RoleDraft $roleDraft): void;

    /**
     * Publishes the given RoleDraft.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft $roleDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to publish this RoleDraft
     */
    public function publishRoleDraft(RoleDraft $roleDraft): void;

    /**
     * Loads a role for the given id.
     *
     * @param int $id
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to read this role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if a role with the given name was not found
     */
    public function loadRole(int $id): Role;

    /**
     * Loads a role for the given identifier.
     *
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to read this role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if a role with the given name was not found
     */
    public function loadRoleByIdentifier(string $identifier): Role;

    /**
     * Loads all roles, excluding the ones the current user is not allowed to read.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role[]
     */
    public function loadRoles(): iterable;

    /**
     * Deletes the given role.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to delete this role
     */
    public function deleteRole(Role $role): void;

    /**
     * Assigns a role to the given user group.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null $roleLimitation an optional role limitation (which is either a subtree limitation or section limitation)
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to assign a role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if $roleLimitation is not valid
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If assignment already exists
     */
    public function assignRoleToUserGroup(Role $role, UserGroup $userGroup, RoleLimitation $roleLimitation = null): void;

    /**
     * Assigns a role to the given user.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $user
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null $roleLimitation an optional role limitation (which is either a subtree limitation or section limitation)
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to assign a role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException if $roleLimitation is not valid
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If assignment already exists
     */
    public function assignRoleToUser(Role $role, User $user, RoleLimitation $roleLimitation = null): void;

    /**
     * Loads a role assignment for the given id.
     *
     * @param int $roleAssignmentId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to read this role
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If the role assignment was not found
     */
    public function loadRoleAssignment(int $roleAssignmentId): RoleAssignment;

    /**
     * Returns the assigned user and user groups to this role.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to read a role
     */
    public function getRoleAssignments(Role $role): iterable;

    /**
     * Returns UserRoleAssignments assigned to the given User, excluding the ones the current user is not allowed to read.
     *
     * If second parameter \$inherited is true then UserGroupRoleAssignment is also returned for UserGroups User is
     * placed in as well as those inherited from parent UserGroups.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $user
     * @param bool $inherited Also return all inherited Roles from UserGroups User belongs to, and it's parents.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserRoleAssignment[]|\Ibexa\Contracts\Core\Repository\Values\User\UserGroupRoleAssignment[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException On invalid User object
     */
    public function getRoleAssignmentsForUser(User $user, bool $inherited = false): iterable;

    /**
     * Returns the UserGroupRoleAssignments assigned to the given UserGroup, excluding the ones the current user is not allowed to read.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroupRoleAssignment[]
     */
    public function getRoleAssignmentsForUserGroup(UserGroup $userGroup): iterable;

    /**
     * Removes the given role assignment.
     *
     * i.e. unassigns a user or a user group from a role with the given limitations
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment $roleAssignment
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the authenticated user is not allowed to remove a role assignment
     */
    public function removeRoleAssignment(RoleAssignment $roleAssignment): void;

    /**
     *  Instantiates a role create class.
     *
     * @param string $name
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct
     */
    public function newRoleCreateStruct(string $name): RoleCreateStruct;

    /**
     *  Instantiates a role copy struct.
     */
    public function newRoleCopyStruct(string $name): RoleCopyStruct;

    /**
     * Instantiates a policy create class.
     *
     * @param string $module
     * @param string $function
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct
     */
    public function newPolicyCreateStruct(string $module, string $function): PolicyCreateStruct;

    /**
     * Instantiates a policy update class.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct
     */
    public function newPolicyUpdateStruct(): PolicyUpdateStruct;

    /**
     * Instantiates a policy update class.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleUpdateStruct
     */
    public function newRoleUpdateStruct(): RoleUpdateStruct;

    /**
     * Returns the LimitationType registered with the given identifier.
     *
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Core\Limitation\Type
     *
     * @throws \RuntimeException On missing Limitation
     */
    public function getLimitationType(string $identifier): Type;

    /**
     * Returns the LimitationType's assigned to a given module/function.
     *
     * Typically used for:
     *  - Internal validation limitation value use on Policies
     *  - Role admin gui for editing policy limitations incl list limitation options via valueSchema()
     *
     * @param string $module Legacy name of "controller", it's a unique identifier like "content"
     * @param string $function Legacy name of a controller "action", it's a unique within the controller like "read"
     *
     * @return \Ibexa\Contracts\Core\Limitation\Type[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If module/function to limitation type mapping
     *                                                                 refers to a non existing identifier.
     */
    public function getLimitationTypesByModuleFunction(string $module, string $function): iterable;
}

class_alias(RoleService::class, 'eZ\Publish\API\Repository\RoleService');
