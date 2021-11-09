<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation
 * @group integration
 * @group limitation
 */
class SectionLimitationTest extends BaseLimitationTest
{
    public function testSectionLimitationAllow()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $readPolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' != $policy->module || 'read' != $policy->function) {
                continue;
            }
            $readPolicy = $policy;
            break;
        }

        if (null === $readPolicy) {
            throw new \ErrorException('No content:read policy found.');
        }

        // Only allow access to the media section
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new SectionLimitation(
                ['limitationValues' => [3]]
            )
        );

        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $readPolicy,
            $policyUpdate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $contentService = $repository->getContentService();

        // Load the images folder
        $images = $contentService->loadContentByRemoteId('e7ff633c6b8e0fd3531e74c6e712bead');
        /* END: Use Case */

        $this->assertEquals(
            'Images',
            $images->getFieldValue('name')->text
        );
    }

    public function testSectionLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $readPolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' != $policy->module || 'read' != $policy->function) {
                continue;
            }
            $readPolicy = $policy;
            break;
        }

        if (null === $readPolicy) {
            throw new \ErrorException('No content:read policy found.');
        }

        // Give access to "Standard" and "Restricted" section
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new SectionLimitation(
                ['limitationValues' => [1, 6]]
            )
        );

        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $readPolicy,
            $policyUpdate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $contentService = $repository->getContentService();

        // This call fails with an UnauthorizedException because the current user
        // cannot access the "Media" section
        $contentService->loadContentByRemoteId('e7ff633c6b8e0fd3531e74c6e712bead');
        /* END: Use Case */
    }
}

class_alias(SectionLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\SectionLimitationTest');
