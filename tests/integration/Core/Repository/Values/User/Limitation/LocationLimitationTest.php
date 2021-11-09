<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LocationLimitation;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\LocationLimitation
 * @group integration
 * @group limitation
 */
class LocationLimitationTest extends BaseLimitationTest
{
    /**
     * Tests a LocationLimitation.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\LocationLimitation
     */
    public function testLocationLimitationAllow()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $parentLocationId = $this->generateId('location', 60);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new LocationLimitation(
                ['limitationValues' => [$parentLocationId]]
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

    public function testLocationLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $parentLocationId = $this->generateId('location', 61);
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');

        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');
        $policyCreate->addLimitation(
            new LocationLimitation(
                ['limitationValues' => [$parentLocationId]]
            )
        );

        $roleDraft = $roleService->createRoleDraft($role);
        $roleService->addPolicyByRoleDraft($roleDraft, $policyCreate);
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $this->createWikiPageDraft();
        /* END: Use Case */
    }
}

class_alias(LocationLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\LocationLimitationTest');
