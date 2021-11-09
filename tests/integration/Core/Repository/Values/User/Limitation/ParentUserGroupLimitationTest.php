<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentUserGroupLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentUserGroupLimitation
 * @group integration
 * @group limitation
 */
class ParentUserGroupLimitationTest extends BaseLimitationTest
{
    public function testParentUserGroupLimitationAllow()
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        $parentUserGroupId = $this->generateId('location', 4);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();
        $currentUser = $userService->loadUser(
            $permissionResolver->getCurrentUserReference()->getUserId()
        );

        $userGroupCreate = $userService->newUserGroupCreateStruct('eng-GB');
        $userGroupCreate->setField('name', 'Shared wiki');

        $userGroup = $userService->createUserGroup(
            $userGroupCreate,
            $userService->loadUserGroup(
                $parentUserGroupId
            )
        );

        // Assign system user and example user to same group
        $userService->assignUserToUserGroup($user, $userGroup);
        $userService->assignUserToUserGroup($currentUser, $userGroup);

        $roleService = $repository->getRoleService();

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentUserGroupLimitation(
                [
                    'limitationValues' => [true],
                ]
            )
        );
        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $roleService->newPolicyCreateStruct('content', 'read')
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUserGroup($role, $userGroup);

        $permissionResolver->setCurrentUserReference($user);

        $draft = $this->createWikiPageDraft();
        /* END: Use Case */

        $this->assertEquals(
            'An awesome wiki page',
            $draft->getFieldValue('title')->text
        );
    }

    public function testParentUserGroupLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        $parentUserGroupId = $this->generateId('location', 4);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $userGroupCreate = $userService->newUserGroupCreateStruct('eng-GB');
        $userGroupCreate->setField('name', 'Shared wiki');

        $userGroup = $userService->createUserGroup(
            $userGroupCreate,
            $userService->loadUserGroup(
                $parentUserGroupId
            )
        );

        // Assign only example user to new group
        $userService->assignUserToUserGroup($user, $userGroup);

        $roleService = $repository->getRoleService();

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentUserGroupLimitation(
                [
                    'limitationValues' => [true],
                ]
            )
        );

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $roleService->newPolicyCreateStruct('content', 'read')
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUserGroup($role, $userGroup);

        $permissionResolver->setCurrentUserReference($user);

        $this->createWikiPageDraft();
        /* END: Use Case */
    }
}

class_alias(ParentUserGroupLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\ParentUserGroupLimitationTest');
