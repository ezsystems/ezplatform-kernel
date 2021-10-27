<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\OwnerLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\OwnerLimitation
 * @group integration
 * @group limitation
 */
class OwnerLimitationTest extends BaseLimitationTest
{
    public function testOwnerLimitationAllow()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $contentService = $repository->getContentService();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $removePolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' != $policy->module || 'remove' != $policy->function) {
                continue;
            }
            $removePolicy = $policy;
            break;
        }

        if (null === $removePolicy) {
            throw new \ErrorException('No content:remove policy found.');
        }

        // Only allow remove for the user's own content
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new OwnerLimitation(
                ['limitationValues' => [1]]
            )
        );
        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $removePolicy,
            $policyUpdate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $content = $this->createWikiPage();

        $metadataUpdate = $contentService->newContentMetadataUpdateStruct();
        $metadataUpdate->ownerId = $user->id;

        $contentService->updateContentMetadata(
            $content->contentInfo,
            $metadataUpdate
        );

        $permissionResolver->setCurrentUserReference($user);

        $contentService->deleteContent(
            $contentService->loadContentInfo($content->id)
        );
        /* END: Use Case */

        $contentService->loadContent($content->id);
    }

    public function testOwnerLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $contentService = $repository->getContentService();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $removePolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' != $policy->module || 'remove' != $policy->function) {
                continue;
            }
            $removePolicy = $policy;
            break;
        }

        if (null === $removePolicy) {
            throw new \ErrorException('No content:remove policy found.');
        }

        // Only allow remove for the user's own content
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new OwnerLimitation(
                ['limitationValues' => [1]]
            )
        );
        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $removePolicy,
            $policyUpdate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $content = $this->createWikiPage();

        $permissionResolver->setCurrentUserReference($user);

        // This call fails with an UnauthorizedException, because the current
        // user is not the content owner
        $contentService->deleteContent(
            $contentService->loadContentInfo($content->id)
        );
        /* END: Use Case */
    }
}

class_alias(OwnerLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\OwnerLimitationTest');
