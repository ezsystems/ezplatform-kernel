<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Features\Context;

use Behat\Behat\Context\Context;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\RoleService;
use PHPUnit\Framework\Assert as Assertion;

/**
 * Sentences for Roles.
 */
class RoleContext implements Context
{
    /** @var \eZ\Publish\API\Repository\roleService */
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Make sure a Role with name $name exists in parent group.
     *
     * @param string $name Role identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function ensureRoleExists($name)
    {
        try {
            $role = $this->roleService->loadRoleByIdentifier($name);
        } catch (ApiExceptions\NotFoundException $e) {
            $roleCreateStruct = $this->roleService->newRoleCreateStruct($name);
            $roleDraft = $this->roleService->createRole($roleCreateStruct);
            $this->roleService->publishRoleDraft($roleDraft);
            $role = $this->roleService->loadRole($roleDraft->id);
        }

        return $role;
    }

    /**
     * Fetches the role with identifier.
     *
     * @param string $identifier Role identifier
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function getRole($identifier)
    {
        $role = null;
        try {
            $role = $this->roleService->loadRoleByIdentifier($identifier);
        } catch (ApiExceptions\NotFoundException $e) {
            // Role not found, do nothing, returns null
        }

        return $role;
    }

    /**
     * @Given a/an :name role exists
     *
     * Ensures a role exists with name ':name', creating a new one if necessary.
     *
     * @return \eZ\Publish\API\Repository\Values\User\Role
     */
    public function iHaveRole($name)
    {
        return $this->ensureRoleExists($name);
    }

    /**
     * @Then I see that a/an :name role exists
     *
     * Verifies that a role with $name exists.
     */
    public function iSeeRole($name)
    {
        $role = $this->getRole($name);
        Assertion::assertNotNull(
            $role,
            "Couldn't find Role with name $name"
        );
    }

    /**
     * @Given :name do not have any assigned policies
     */
    public function noAssginedPolicies($name)
    {
        $role = $this->getRole($name);
        Assertion::assertNotNull(
            $role,
            "Couldn't find Role with name $name"
        );
        $policies = $role->getPolicies();
        Assertion::assertEmpty($policies, "Role $name has policies associated");
    }

    /**
     * @Given :name do not have any assigned Users and groups
     */
    public function noAssigneGroups($name)
    {
        $role = $this->getRole($name);
        Assertion::assertNotNull(
            $role,
            "Couldn't find Role with name $name"
        );
        $roleAssigments = $this->roleService->getRoleAssignments($role);
        Assertion::assertEmpty($roleAssigments, "Role $name has Users or groups associated");
    }

    /**
     * @Then I see that a/an :name role does not exists
     *
     * Verifies that a role with $name exists.
     */
    public function iDontSeeRole($name)
    {
        $role = $this->getRole($name);
        Assertion::assertNull(
            $role,
            "Found Role with name $name"
        );
    }
}
