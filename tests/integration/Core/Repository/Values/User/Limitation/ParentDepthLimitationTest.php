<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ContentTypeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentDepthLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentDepthLimitation
 * @group integration
 * @group limitation
 */
class ParentDepthLimitationTest extends BaseLimitationTest
{
    public function testParentDepthLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $contentTypeId = $this->generateId('contentType', 22);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentDepthLimitation(
                ['limitationValues' => [3]]
            )
        );
        $policyCreate->addLimitation(
            new ContentTypeLimitation(
                ['limitationValues' => [$contentTypeId]]
            )
        );

        $role = $this->addPolicyToRole('Editor', $policyCreate);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $draft = $this->createWikiPageDraft();
        /* END: Use Case */

        $this->assertEquals(
            'An awesome wiki page',
            $draft->getFieldValue('title')->text
        );
    }

    public function testParentDepthLimitationAllow()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $contentTypeId = $this->generateId('contentType', 22);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentDepthLimitation(
                ['limitationValues' => [1, 2, 3, 4]]
            )
        );
        $policyCreate->addLimitation(
            new ContentTypeLimitation(
                ['limitationValues' => [$contentTypeId]]
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

    /**
     * Tests a combination of ParentDepthLimitation and ContentTypeLimitation.
     *
     * @depends testParentDepthLimitationAllow
     */
    public function testParentDepthLimitationAllowPublish()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $contentTypeId = $this->generateId('contentType', 22);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentDepthLimitation(
                ['limitationValues' => [1, 2, 3, 4]]
            )
        );
        $policyCreate->addLimitation(
            new ContentTypeLimitation(
                ['limitationValues' => [$contentTypeId]]
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

        $draft = $this->createWikiPageDraft();

        $contentService = $repository->getContentService();

        $content = $contentService->publishVersion($draft->versionInfo);
        /* END: Use Case */
    }
}

class_alias(ParentDepthLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\ParentDepthLimitationTest');
