<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\API\Repository\Tests\Values\User\Limitation;

use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\User\Limitation\StatusLimitation;

/**
 * Test case for the {@link \eZ\Publish\API\Repository\Values\User\Limitation\StatusLimitation}
 * class.
 *
 * @see eZ\Publish\API\Repository\Values\User\Limitation
 * @see eZ\Publish\API\Repository\Values\User\Limitation\StatusLimitation
 * @group integration
 * @group limitation
 */
class StatusLimitationTest extends BaseLimitationTest
{
    /**
     * Tests a StatusLimitation.
     *
     * @see eZ\Publish\API\Repository\Values\User\Limitation\StatusLimitation
     */
    public function testStatusLimitationAllow()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $administratorUserId = $this->generateId('user', 14);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $administratorUserId is the ID of the "Administrator" user in a eZ
        // Publish demo installation.
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.

        // Load the user service
        $userService = $repository->getUserService();

        // Load the "Administrator" user and set it as current user
        $administratorUser = $userService->loadUser($administratorUserId);
        $permissionResolver->setCurrentUserReference($administratorUser);

        // Create a Content draft with "Administrator" user
        $draft = $this->createWikiPageDraft();

        $roleService = $repository->getRoleService();

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'versionread');
        $policyCreate->addLimitation(
            new StatusLimitation(
                ['limitationValues' => [VersionInfo::STATUS_DRAFT]]
            )
        );

        // Add policy to load draft versions to "Anonymous" role
        $this->addPolicyToRole('Anonymous', $policyCreate);

        // Load the user service
        $userService = $repository->getUserService();

        // Load "Anonymous User" (which has "Anonymous" role)
        $anonymousUser = $userService->loadUser($anonymousUserId);

        // Set it as current user
        $permissionResolver->setCurrentUserReference($anonymousUser);

        $contentService = $repository->getContentService();

        // Try to load Administrator draft with Anonymous User
        // This will succeed because required policy was previously set to the Anonymous role
        $loadedDraft = $contentService->loadContent(
            $draft->getVersionInfo()->getContentInfo()->id,
            null,
            $draft->getVersionInfo()->versionNo
        );
        /* END: Use Case */

        $this->assertEquals(
            'An awesome wiki page',
            $loadedDraft->getFieldValue('title')->text
        );
    }

    /**
     * Tests a StatusLimitation.
     *
     * @see eZ\Publish\API\Repository\Values\User\Limitation\StatusLimitation
     */
    public function testStatusLimitationForbid()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $administratorUserId = $this->generateId('user', 14);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // $administratorUserId is  the ID of the "Administrator" user in a eZ
        // Publish demo installation.

        // Load the user service
        $userService = $repository->getUserService();

        // Load the "Administrator" user and set it as current user
        $administratorUser = $userService->loadUser($administratorUserId);
        $permissionResolver->setCurrentUserReference($administratorUser);

        // Create a Content draft with "Administrator" user
        $draft = $this->createWikiPageDraft();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Anonymous');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'versionread');
        $policyCreate->addLimitation(
            new StatusLimitation(
                ['limitationValues' => [VersionInfo::STATUS_PUBLISHED]]
            )
        );

        // Add policy to load published versions to "Anonymous" role
        $roleDraft = $roleService->createRoleDraft($role);
        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->publishRoleDraft($roleDraft);

        // Load the user service
        $userService = $repository->getUserService();

        // Load anonymous user (which has "Anonymous" role)
        $anonymousUser = $userService->loadUser($anonymousUserId);

        // Set it as current user
        $permissionResolver->setCurrentUserReference($anonymousUser);

        $contentService = $repository->getContentService();

        // Try to load Administrator user draft with "Anonymous User"
        // This will fail with "UnauthorizedException" because we allowed users with
        // "Anonymous" role to read only published versions
        $loadedDraft = $contentService->loadContent(
            $draft->getVersionInfo()->getContentInfo()->id,
            null,
            $draft->getVersionInfo()->versionNo
        );
        /* END: Use Case */
    }
}
