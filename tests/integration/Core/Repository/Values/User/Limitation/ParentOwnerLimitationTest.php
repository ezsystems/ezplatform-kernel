<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentOwnerLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentOwnerLimitation
 * @group integration
 * @group limitation
 */
class ParentOwnerLimitationTest extends BaseLimitationTest
{
    public function testParentOwnerLimitationAllow()
    {
        $repository = $this->getRepository();

        $parentContentId = $this->generateId('content', 58);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentOwnerLimitation(
                ['limitationValues' => [1]]
            )
        );

        $role = $this->addPolicyToRole('Editor', $policyCreate);

        $roleService->assignRoleToUser($role, $user);

        $contentService = $repository->getContentService();

        $metadataUpdate = $contentService->newContentMetadataUpdateStruct();
        $metadataUpdate->ownerId = $user->id;

        $contentService->updateContentMetadata(
            $contentService->loadContentInfo($parentContentId),
            $metadataUpdate
        );

        $permissionResolver->setCurrentUserReference($user);

        $draft = $this->createWikiPageDraft();
        /* END: Use Case */

        $this->assertEquals(
            'An awesome wiki page',
            $draft->getFieldValue('title')->text
        );
    }

    public function testParentOwnerLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentOwnerLimitation(
                ['limitationValues' => [1]]
            )
        );

        $roleDraft = $roleService->createRoleDraft($role);
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $this->createWikiPageDraft();
        /* END: Use Case */
    }
}

class_alias(ParentOwnerLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\ParentOwnerLimitationTest');
