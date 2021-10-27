<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;

/**
 * Test case for operations in the RoleService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\RoleService
 * @group integration
 * @group authorization
 */
class RoleServiceAuthorizationTest extends BaseTest
{
    /**
     * Test for the createRole() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::createRole()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testCreateRole
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testCreateRoleThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // Get the role service
        $roleService = $repository->getRoleService();

        // Instantiate a role create struct.
        $roleCreate = $roleService->newRoleCreateStruct('roleName');

        // This call will fail with an "UnauthorizedException"
        $roleService->createRole($roleCreate);
        /* END: Use Case */
    }

    /**
     * Test for the loadRole() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::loadRole()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testLoadRole
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testLoadRoleThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->loadRole($role->id);
        /* END: Use Case */
    }

    /**
     * Test for the loadRoleByIdentifier() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::loadRoleByIdentifier()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testLoadRoleByIdentifier
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testLoadRoleByIdentifierThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->loadRoleByIdentifier($role->identifier);
        /* END: Use Case */
    }

    /**
     * Test for the loadRoles() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::loadRoles()
     */
    public function testLoadRolesLoadsEmptyListForAnonymousUser()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // Get the role service
        $roleService = $repository->getRoleService();
        /* END: Use Case */

        $this->assertEquals([], $roleService->loadRoles());
    }

    /**
     * Test for the loadRoles() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::loadRoles()
     */
    public function testLoadRolesForUserWithSubtreeLimitation()
    {
        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();

        /* BEGIN: Use Case */
        // create user that can read/create/delete but cannot edit or content
        $this->createRoleWithPolicies('roleReader', [
            ['module' => 'role', 'function' => 'read'],
        ]);

        $user = $this->createCustomUserWithLogin(
            'user',
            'user@example.com',
            'roleReaders',
            'roleReader',
            new SubtreeLimitation(['limitationValues' => ['/1/2/']])
        );

        $repository->getPermissionResolver()->setCurrentUserReference($user);
        /* END: Use Case */

        $this->assertCount(6, $roleService->loadRoles());
    }

    /**
     * Test for the deleteRole() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::deleteRole()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testDeleteRole
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testDeleteRoleThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->deleteRole($role);
        /* END: Use Case */
    }

    /**
     * Test for the updatePolicy() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::updatePolicyByRoleDraft()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testUpdatePolicyByRoleDraft
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testUpdatePolicyByRoleDraftThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();
        $roleDraft = $roleService->createRoleDraft($role);
        // Get first role policy
        $policies = $roleDraft->getPolicies();
        $policy = reset($policies);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // Get a policy update struct and add a limitation
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new SubtreeLimitation(
                [
                    'limitationValues' => ['/1/'],
                ]
            )
        );

        // This call will fail with an "UnauthorizedException"
        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $policy,
            $policyUpdate
        );
        $roleService->publishRoleDraft($roleDraft);
        /* END: Use Case */
    }

    /**
     * Test for the removePolicy() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::removePolicy()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testRemovePolicyByRoleDraft
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testRemovePolicyThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleCreate = $roleService->newRoleCreateStruct('newRole');

        // @todo uncomment when support for multilingual names and descriptions is added EZP-24776
        // $roleCreate->mainLanguageCode = 'eng-US';

        // Create a new role with two policies
        $roleDraft = $roleService->createRole($roleCreate);
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $roleService->newPolicyCreateStruct('content', 'create')
        );
        $roleDraft = $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $roleService->newPolicyCreateStruct('content', 'delete')
        );

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->removePolicyByRoleDraft($roleDraft, $roleDraft->getPolicies()[0]);
        /* END: Use Case */
    }

    /**
     * Test for the removePolicyByRoleDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::removePolicyByRoleDraft()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testRemovePolicyByRoleDraft
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testDeletePolicyThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Get first role policy
        $roleDraft = $roleService->createRoleDraft($role);
        $policiesDrafts = $roleDraft->getPolicies();
        $policyDraft = reset($policiesDrafts);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->removePolicyByRoleDraft($roleDraft, $policyDraft);
        /* END: Use Case */
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::assignRoleToUserGroup()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testAssignRoleToUserGroup
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testAssignRoleToUserGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Load the "Editors" user group
        $userGroup = $userService->loadUserGroup($editorsGroupId);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->assignRoleToUserGroup($role, $userGroup);
        /* END: Use Case */
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::assignRoleToUserGroup($role, $userGroup, $roleLimitation)
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testAssignRoleToUserGroup
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testAssignRoleToUserGroupThrowsUnauthorizedExceptionWithRoleLimitationParameter()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Load the "Editors" user group
        $userGroup = $userService->loadUserGroup($editorsGroupId);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // Create a subtree role limitation
        $limitation = new SubtreeLimitation(
            [
                'limitationValues' => ['/1/2/'],
            ]
        );

        // This call will fail with an "UnauthorizedException"
        $roleService->assignRoleToUserGroup($role, $userGroup, $limitation);
        /* END: Use Case */
    }

    /**
     * Test for the removeRoleAssignment() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::removeRoleAssignment()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testRemoveRoleAssignmentFromUserGroup
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testRemoveRoleAssignmentFromUserGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Load the "Editors" user group
        $userGroup = $userService->loadUserGroup($editorsGroupId);

        // Assign new role to "Editors" user group
        $roleService->assignRoleToUserGroup($role, $userGroup);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        $roleAssignments = $roleService->getRoleAssignmentsForUserGroup($userGroup);

        // This call will fail with an "UnauthorizedException"
        foreach ($roleAssignments as $roleAssignment) {
            if ($roleAssignment->role->id === $role->id) {
                $roleService->removeRoleAssignment($roleAssignment);
            }
        }
        /* END: Use Case */
    }

    /**
     * Test for the assignRoleToUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::assignRoleToUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testAssignRoleToUser
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testAssignRoleToUserThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->assignRoleToUser($role, $user);
        /* END: Use Case */
    }

    /**
     * Test for the assignRoleToUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::assignRoleToUser($role, $user, $roleLimitation)
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testAssignRoleToUser
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testAssignRoleToUserThrowsUnauthorizedExceptionWithRoleLimitationParameter()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // Create a subtree role limitation
        $limitation = new SubtreeLimitation(
            [
                'limitationValues' => ['/1/2/'],
            ]
        );

        // This call will fail with an "UnauthorizedException"
        $roleService->assignRoleToUser($role, $user, $limitation);
        /* END: Use Case */
    }

    /**
     * Test for the removeRoleAssignment() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::removeRoleAssignment()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testRemoveRoleAssignment
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testRemoveRoleAssignmentThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Assign new role to "Editor" user
        $roleService->assignRoleToUser($role, $user);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);
        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);

        // This call will fail with an "UnauthorizedException"
        foreach ($roleAssignments as $roleAssignment) {
            if ($roleAssignment->role->id === $role->id) {
                $roleService->removeRoleAssignment($roleAssignment);
            }
        }
        /* END: Use Case */
    }

    /**
     * Test for the getRoleAssignments() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::getRoleAssignments()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testGetRoleAssignments
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testGetRoleAssignmentsThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $role = $this->createRole();

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->getRoleAssignments($role);
        /* END: Use Case */
    }

    /**
     * Test for the getRoleAssignmentsForUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::getRoleAssignmentsForUser()
     */
    public function testGetRoleAssignmentsForUserLoadsEmptyListForAnonymousUser()
    {
        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $this->createRole();

        // Set "Editor" user as current user.
        $repository->getPermissionResolver()->setCurrentUserReference($user);
        /* END: Use Case */

        $this->assertSame([], $roleService->getRoleAssignmentsForUser($user));
    }

    /**
     * Test for the getRoleAssignmentsForUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::getRoleAssignmentsForUser()
     */
    public function testGetRoleAssignmentsForUserWithSubtreeLimitation()
    {
        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();

        /* BEGIN: Use Case */
        $user = $this->createUserWithPolicies(
            'trash_test_user',
            [
                ['module' => 'role', 'function' => 'read'],
            ],
            new SubtreeLimitation(['limitationValues' => ['/1/2/']])
        );

        $repository->getPermissionResolver()->setCurrentUserReference($user);
        /* END: Use Case */

        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);
        $this->assertCount(1, $roleAssignments);

        $roleAssignment = $roleAssignments[0];
        $this->assertSame($user, $roleAssignment->user);
    }

    /**
     * Test for the getRoleAssignmentsForUserGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\RoleService::getRoleAssignmentsForUserGroup()
     * @depends Ibexa\Tests\Integration\Core\Repository\RoleServiceTest::testGetRoleAssignmentsForUserGroup
     * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
     */
    public function testGetRoleAssignmentsForUserGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $roleService = $repository->getRoleService();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $this->createRole();

        // Load the "Editors" user group
        $userGroup = $userService->loadUserGroup($editorsGroupId);

        // Set "Editor" user as current user.
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $roleService->getRoleAssignmentsForUserGroup($userGroup);
        /* END: Use Case */
    }

    /**
     * Create a role fixture in a variable named <b>$role</b>,.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Role
     */
    private function createRole()
    {
        $repository = $this->getRepository();

        /* BEGIN: Inline */
        // Get the role service
        $roleService = $repository->getRoleService();

        // Get new policy create struct
        $policyCreate = $roleService->newPolicyCreateStruct('content', '*');

        // Get a role create struct instance and set properties
        $roleCreate = $roleService->newRoleCreateStruct('testRole');

        // @todo uncomment when support for multilingual names and descriptions is added EZP-24776
        // $roleCreate->mainLanguageCode = 'eng-GB';

        $roleCreate->addPolicy($policyCreate);

        // Create a new role instance.
        $roleDraft = $roleService->createRole($roleCreate);
        $roleService->publishRoleDraft($roleDraft);
        $role = $roleService->loadRole($roleDraft->id);
        /* END: Inline */

        return $role;
    }
}

class_alias(RoleServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\RoleServiceAuthorizationTest');
