<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Mapper;

use Ibexa\Contracts\Core\Persistence\User\Policy as SPIPolicy;
use Ibexa\Contracts\Core\Persistence\User\Role as SPIRole;
use Ibexa\Contracts\Core\Persistence\User\RoleAssignment as SPIRoleAssignment;
use Ibexa\Contracts\Core\Persistence\User\RoleCopyStruct as SPIRoleCopyStruct;
use Ibexa\Contracts\Core\Persistence\User\RoleCreateStruct as SPIRoleCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\Role as APIRole;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCopyStruct as APIRoleCopyStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct as APIRoleCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\Values\User\Policy;
use Ibexa\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\Core\Repository\Values\User\RoleDraft;
use Ibexa\Core\Repository\Values\User\UserGroupRoleAssignment;
use Ibexa\Core\Repository\Values\User\UserRoleAssignment;

/**
 * Internal service to map Role objects between API and SPI values.
 *
 * @internal Meant for internal use by Repository.
 */
class RoleDomainMapper
{
    /** @var \Ibexa\Core\Repository\Permission\LimitationService */
    protected $limitationService;

    /**
     * @param \Ibexa\Core\Repository\Permission\LimitationService $limitationService
     */
    public function __construct(LimitationService $limitationService)
    {
        $this->limitationService = $limitationService;
    }

    /**
     * Maps provided SPI Role value object to API Role value object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\User\Role $role
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role
     */
    public function buildDomainRoleObject(SPIRole $role)
    {
        $rolePolicies = [];
        foreach ($role->policies as $spiPolicy) {
            $rolePolicies[] = $this->buildDomainPolicyObject($spiPolicy);
        }

        return new Role(
            [
                'id' => $role->id,
                'identifier' => $role->identifier,
                'status' => $role->status,
                'policies' => $rolePolicies,
            ]
        );
    }

    /**
     * Builds a RoleDraft domain object from value object returned by persistence
     * Decorates Role.
     *
     * @param \Ibexa\Contracts\Core\Persistence\User\Role $spiRole
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft
     */
    public function buildDomainRoleDraftObject(SPIRole $spiRole)
    {
        return new RoleDraft(
            [
                'innerRole' => $this->buildDomainRoleObject($spiRole),
            ]
        );
    }

    /**
     * Maps provided SPI Policy value object to API Policy value object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\User\Policy $spiPolicy
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Policy|\Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft
     */
    public function buildDomainPolicyObject(SPIPolicy $spiPolicy)
    {
        $policyLimitations = [];
        if ($spiPolicy->module !== '*' && $spiPolicy->function !== '*' && $spiPolicy->limitations !== '*') {
            foreach ($spiPolicy->limitations as $identifier => $values) {
                $policyLimitations[] = $this->limitationService->getLimitationType($identifier)->buildValue($values);
            }
        }

        $policy = new Policy(
            [
                'id' => $spiPolicy->id,
                'roleId' => $spiPolicy->roleId,
                'module' => $spiPolicy->module,
                'function' => $spiPolicy->function,
                'limitations' => $policyLimitations,
            ]
        );

        // Original ID is set on SPI policy, which means that it's a draft.
        if ($spiPolicy->originalId) {
            $policy = new PolicyDraft(['innerPolicy' => $policy, 'originalId' => $spiPolicy->originalId]);
        }

        return $policy;
    }

    /**
     * Builds the API UserRoleAssignment object from provided SPI RoleAssignment object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\User\RoleAssignment $spiRoleAssignment
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $user
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserRoleAssignment
     */
    public function buildDomainUserRoleAssignmentObject(SPIRoleAssignment $spiRoleAssignment, User $user, APIRole $role)
    {
        $limitation = null;
        if (!empty($spiRoleAssignment->limitationIdentifier)) {
            $limitation = $this
                ->limitationService
                ->getLimitationType($spiRoleAssignment->limitationIdentifier)
                ->buildValue($spiRoleAssignment->values);
        }

        return new UserRoleAssignment(
            [
                'id' => $spiRoleAssignment->id,
                'limitation' => $limitation,
                'role' => $role,
                'user' => $user,
            ]
        );
    }

    /**
     * Builds the API UserGroupRoleAssignment object from provided SPI RoleAssignment object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\User\RoleAssignment $spiRoleAssignment
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Role $role
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroupRoleAssignment
     */
    public function buildDomainUserGroupRoleAssignmentObject(SPIRoleAssignment $spiRoleAssignment, UserGroup $userGroup, APIRole $role)
    {
        $limitation = null;
        if (!empty($spiRoleAssignment->limitationIdentifier)) {
            $limitation = $this
                ->limitationService
                ->getLimitationType($spiRoleAssignment->limitationIdentifier)
                ->buildValue($spiRoleAssignment->values);
        }

        return new UserGroupRoleAssignment(
            [
                'id' => $spiRoleAssignment->id,
                'limitation' => $limitation,
                'role' => $role,
                'userGroup' => $userGroup,
            ]
        );
    }

    /**
     * Creates SPI Role create struct from provided API role create struct.
     */
    public function buildPersistenceRoleCreateStruct(APIRoleCreateStruct $roleCreateStruct): SPIRoleCreateStruct
    {
        $policiesToCreate = $this->fillRoleStructWithPolicies($roleCreateStruct);

        return new SPIRoleCreateStruct(
            [
                'identifier' => $roleCreateStruct->identifier,
                'policies' => $policiesToCreate,
            ]
        );
    }

    /**
     * Creates SPI Role copy struct from provided API role copy struct.
     */
    public function buildPersistenceRoleCopyStruct(APIRoleCopyStruct $roleCopyStruct, int $clonedId, int $status): SPIRoleCopyStruct
    {
        $policiesToCopy = $this->fillRoleStructWithPolicies($roleCopyStruct);

        return new SPIRoleCopyStruct(
            [
                'clonedId' => $clonedId,
                'newIdentifier' => $roleCopyStruct->newIdentifier,
                'status' => $status,
                'policies' => $policiesToCopy,
            ]
        );
    }

    protected function fillRoleStructWithPolicies(APIRoleCreateStruct $struct): array
    {
        $policies = [];
        foreach ($struct->getPolicies() as $policyStruct) {
            $policies[] = $this->buildPersistencePolicyObject(
                $policyStruct->module,
                $policyStruct->function,
                $policyStruct->getLimitations()
            );
        }

        return $policies;
    }

    /**
     * Creates SPI Policy value object from provided module, function and limitations.
     *
     * @param string $module
     * @param string $function
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] $limitations
     *
     * @return \Ibexa\Contracts\Core\Persistence\User\Policy
     */
    public function buildPersistencePolicyObject($module, $function, array $limitations)
    {
        $limitationsToCreate = '*';
        if ($module !== '*' && $function !== '*' && !empty($limitations)) {
            $limitationsToCreate = [];
            foreach ($limitations as $limitation) {
                $limitationsToCreate[$limitation->getIdentifier()] = $limitation->limitationValues;
            }
        }

        return new SPIPolicy(
            [
                'module' => $module,
                'function' => $function,
                'limitations' => $limitationsToCreate,
            ]
        );
    }
}

class_alias(RoleDomainMapper::class, 'eZ\Publish\Core\Repository\Mapper\RoleDomainMapper');
