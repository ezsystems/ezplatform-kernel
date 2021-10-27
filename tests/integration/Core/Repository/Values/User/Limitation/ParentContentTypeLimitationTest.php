<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ContentTypeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ParentContentTypeLimitation
 * @group integration
 * @group limitation
 */
class ParentContentTypeLimitationTest extends BaseLimitationTest
{
    public function testParentContentTypeLimitationAllow()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $parentContentTypeId = $this->generateId('contentType', 20);
        $contentTypeId = $this->generateId('contentType', 22);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();
        $contentService = $repository->getContentService();

        $role = $roleService->loadRoleByIdentifier('Editor');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentContentTypeLimitation(
                ['limitationValues' => [$parentContentTypeId]]
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
        $content = $contentService->publishVersion($draft->versionInfo);
        /* END: Use Case */

        $this->assertEquals(
            'An awesome wiki page',
            $content->getFieldValue('title')->text
        );
    }

    public function testParentContentTypeLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $parentContentTypeId = $this->generateId('contentType', 20);
        $contentTypeId = $this->generateId('contentType', 33);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new ParentContentTypeLimitation(
                ['limitationValues' => [$parentContentTypeId]]
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

        $permissionResolver->setCurrentUserReference($user);

        $this->createWikiPageDraft();
        /* END: Use Case */
    }
}

class_alias(ParentContentTypeLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\ParentContentTypeLimitationTest');
