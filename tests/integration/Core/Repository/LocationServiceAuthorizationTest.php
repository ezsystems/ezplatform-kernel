<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LanguageLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\OwnerLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;

/**
 * Test case for operations in the LocationService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\LocationService
 * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testCreateUser
 * @group integration
 * @group authorization
 */
class LocationServiceAuthorizationTest extends BaseTest
{
    /**
     * Test for the createLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::createLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testCreateLocation
     */
    public function testCreateLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();

        $user = $this->createUserVersion1();

        // ContentInfo for "Editors" user group
        $contentInfo = $contentService->loadContentInfo($editorsGroupId);

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        $locationCreate = $locationService->newLocationCreateStruct(1);
        $locationCreate->priority = 23;
        $locationCreate->hidden = true;
        $locationCreate->remoteId = 'sindelfingen';
        $locationCreate->sortField = Location::SORT_FIELD_NODE_ID;
        $locationCreate->sortOrder = Location::SORT_ORDER_DESC;

        // This call will fail with an "UnauthorizedException"
        $locationService->createLocation(
            $contentInfo,
            $locationCreate
        );
        /* END: Use Case */
    }

    /**
     * Test for the createLocation() method. Tests a case when user doesn't have content/manage_locations policy for the new location ID.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::createLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testCreateLocation
     */
    public function testCreateLocationThrowsUnauthorizedExceptionDueToLackOfContentManageLocationsPolicy()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $mediaDirectoryLocationId = $this->generateId('location', '43');

        /* BEGIN: Use Case */
        $locationService = $repository->getLocationService();
        // Location for "Media" directory
        $contentLocation = $locationService->loadLocation($mediaDirectoryLocationId);

        // Create the new "Dummy" user group
        $userService = $repository->getUserService();
        $userGroupCreateStruct = $userService->newUserGroupCreateStruct('eng-GB');
        $userGroupCreateStruct->setField('name', 'Dummy');
        $dummyUserGroup = $userService->createUserGroup($userGroupCreateStruct, $userService->loadUserGroup(4));

        // Create the new "Dummy" role with content/* policy limited by Subtree to "Media" folder
        $roleService = $repository->getRoleService();
        $role = $this->createRoleWithPolicies('Dummy', [
            [
                'module' => 'content',
                'function' => 'read',
                'limitations' => [],
            ],
            [
                'module' => 'content',
                'function' => 'manage_locations',
                'limitations' => [new SubtreeLimitation(['limitationValues' => [$contentLocation->pathString]])],
            ],
        ]);

        $user = $this->createUser('johndoe', 'John', 'Doe', $dummyUserGroup);
        $roleService->assignRoleToUser($role, $user);
        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        $locationCreateStruct = $locationService->newLocationCreateStruct('2');
        $locationCreateStruct->priority = 12;
        $locationCreateStruct->hidden = false;
        $locationCreateStruct->sortField = Location::SORT_FIELD_NODE_ID;
        $locationCreateStruct->sortOrder = Location::SORT_ORDER_DESC;

        // This call will fail with an "UnauthorizedException"
        $locationService->createLocation(
            $contentLocation->contentInfo,
            $locationCreateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::loadLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testLoadLocation
     */
    public function testLoadLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $locationService = $repository->getLocationService();

        $user = $this->createUserVersion1();

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->loadLocation($editorsGroupId);
        /* END: Use Case */
    }

    /**
     * Test for the loadLocationList() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::loadLocationList
     */
    public function testLoadLocationListFiltersUnauthorizedLocations(): void
    {
        $repository = $this->getRepository();
        $locationService = $repository->getLocationService();

        // Set current user to newly created user (with no rights)
        $repository->getPermissionResolver()->setCurrentUserReference(
            $this->createUserVersion1()
        );

        $locations = $locationService->loadLocationList([13]);

        self::assertIsIterable($locations);
        self::assertCount(0, $locations);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::loadLocationByRemoteId()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testLoadLocationByRemoteId
     */
    public function testLoadLocationByRemoteIdThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        // remoteId of the "Editors" location in an eZ Publish demo installation
        $editorsRemoteId = 'f7dda2854fc68f7c8455d9cb14bd04a9';

        $locationService = $repository->getLocationService();

        $user = $this->createUserVersion1();

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->loadLocationByRemoteId($editorsRemoteId);
        /* END: Use Case */
    }

    /**
     * Test for the loadLocations() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::loadLocations()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testLoadLocations
     */
    public function testLoadLocationsNoAccess()
    {
        $repository = $this->getRepository();
        $locationService = $repository->getLocationService();

        $editorsGroupId = $this->generateId('group', 13);
        $editorGroupContentInfo = $repository->getContentService()->loadContentInfo($editorsGroupId);

        // this should return one location for admin
        $locations = $locationService->loadLocations($editorGroupContentInfo);
        $this->assertCount(1, $locations);
        $this->assertInstanceOf(Location::class, $locations[0]);

        $user = $this->createUserVersion1();

        // Set current user to newly created user
        $repository->getPermissionResolver()->setCurrentUserReference($user);

        // This should return empty array given current user does not have read access
        $locations = $locationService->loadLocations($editorGroupContentInfo);
        $this->assertEmpty($locations);
    }

    /**
     * Test for the updateLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::updateLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testUpdateLocation
     */
    public function testUpdateLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $locationService = $repository->getLocationService();

        $originalLocation = $locationService->loadLocation($editorsGroupId);

        $locationUpdateStruct = $locationService->newLocationUpdateStruct();
        $locationUpdateStruct->priority = 3;
        $locationUpdateStruct->remoteId = 'c7adcbf1e96bc29bca28c2d809d0c7ef69272651';
        $locationUpdateStruct->sortField = Location::SORT_FIELD_PRIORITY;
        $locationUpdateStruct->sortOrder = Location::SORT_ORDER_DESC;

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->updateLocation(
            $originalLocation,
            $locationUpdateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the swapLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::swapLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testSwapLocation
     */
    public function testSwapLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $mediaLocationId = $this->generateId('location', 43);
        $demoDesignLocationId = $this->generateId('location', 56);
        /* BEGIN: Use Case */
        // $mediaLocationId is the ID of the "Media" Location in
        // an eZ Publish demo installation

        // $demoDesignLocationId is the ID of the "Demo Design" Location in an eZ
        // Publish demo installation

        // Load the location service
        $locationService = $repository->getLocationService();

        $mediaLocation = $locationService->loadLocation($mediaLocationId);
        $demoDesignLocation = $locationService->loadLocation($demoDesignLocationId);

        // Swaps the content referred to by the locations
        $locationService->swapLocation($mediaLocation, $demoDesignLocation);

        $user = $this->createMediaUserVersion1();

        // Set media editor as current user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->swapLocation($mediaLocation, $demoDesignLocation);
        /* END: Use Case */
    }

    /**
     * Test for the hideLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::hideLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testHideLocation
     */
    public function testHideLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $locationService = $repository->getLocationService();

        $visibleLocation = $locationService->loadLocation($editorsGroupId);

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->hideLocation($visibleLocation);
        /* END: Use Case */
    }

    /**
     * Test for the unhideLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::unhideLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testUnhideLocation
     */
    public function testUnhideLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $locationService = $repository->getLocationService();

        $visibleLocation = $locationService->loadLocation($editorsGroupId);

        // Hide location
        $hiddenLocation = $locationService->hideLocation($visibleLocation);

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->unhideLocation($hiddenLocation);
        /* END: Use Case */
    }

    /**
     * Test for the deleteLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::deleteLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testDeleteLocation
     */
    public function testDeleteLocationThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $editorsGroupId = $this->generateId('group', 13);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation($editorsGroupId);

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->deleteLocation($location);
        /* END: Use Case */
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::deleteLocation()
     */
    public function testDeleteLocationThrowsUnauthorizedExceptionWithLanguageLimitation(): void
    {
        $repository = $this->getRepository();
        $mediaLocationId = $this->generateId('location', 43);

        $locationService = $repository->getLocationService();
        $location = $locationService->loadLocation($mediaLocationId);

        $limitations = [
            new LanguageLimitation(['limitationValues' => ['ger-DE']]),
        ];

        $user = $this->createUserWithPolicies(
            'user',
            [
                ['module' => 'content', 'function' => 'remove', 'limitations' => $limitations],
                ['module' => 'content', 'function' => 'read'],
                ['module' => 'content', 'function' => 'manage_locations'],
            ]
        );

        $permissionResolver = $repository->getPermissionResolver();
        $permissionResolver->setCurrentUserReference($user);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'remove\' \'content\'/');

        $locationService->deleteLocation($location);
    }

    /**
     * Test for the deleteLocation() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::deleteLocation()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testDeleteLocation
     */
    public function testDeleteLocationWithSubtreeThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('The User does not have the \'remove\' \'content\' permission');

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $parentLocationId = $this->generateId('location', 43);
        $administratorUserId = $this->generateId('user', 14);

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);

        $removePolicy = null;
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
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

        // Update content/remove policy to only allow removal of the user's own content
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

        // Set current user to newly created user
        $permissionResolver->setCurrentUserReference($user);

        $locationService = $repository->getLocationService();
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $userService = $repository->getUserService();

        // Create and publish Content with Location under $parentLocationId
        $contentType = $contentTypeService->loadContentTypeByIdentifier('folder');

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->setField('name', 'My awesome possibly deletable folder');
        $contentCreateStruct->alwaysAvailable = true;

        $locationCreateStruct = $locationService->newLocationCreateStruct($parentLocationId);

        $contentDraft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $content = $contentService->publishVersion($contentDraft->versionInfo);

        // New user will be able to delete this Location at this point
        $firstLocation = $locationService->loadLocation($content->contentInfo->mainLocationId);

        // Set current user to administrator user
        $administratorUser = $userService->loadUser($administratorUserId);
        $permissionResolver->setCurrentUserReference($administratorUser);

        // Under newly created Location create Content with administrator user
        // After this created user will not be able to delete $firstLocation
        $locationCreateStruct = $locationService->newLocationCreateStruct($firstLocation->id);
        $contentDraft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $content = $contentService->publishVersion($contentDraft->versionInfo);
        $secondLocation = $locationService->loadLocation($content->contentInfo->mainLocationId);

        // Set current user to newly created user again, and try to delete $firstLocation
        $permissionResolver->setCurrentUserReference($user);

        $this->refreshSearch($repository);

        // This call will fail with an "UnauthorizedException" because user does not have
        // permission to delete $secondLocation which is in the subtree of the $firstLocation
        $locationService->deleteLocation($firstLocation);
        /* END: Use Case */
    }

    /**
     * Test for the copySubtree() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::copySubtree()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testCopySubtree
     */
    public function testCopySubtreeThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $mediaLocationId = $this->generateId('location', 43);
        $demoDesignLocationId = $this->generateId('location', 56);
        /* BEGIN: Use Case */
        $user = $this->createMediaUserVersion1();

        // $mediaLocationId is the ID of the "Media" Location in
        // an eZ Publish demo installation

        // $demoDesignLocationId is the ID of the "Demo Design" Location in an eZ
        // Publish demo installation

        // Load the location service
        $locationService = $repository->getLocationService();

        // Load location to copy
        $locationToCopy = $locationService->loadLocation($mediaLocationId);

        // Load new parent location
        $newParentLocation = $locationService->loadLocation($demoDesignLocationId);

        // Set media editor as current user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->copySubtree(
            $locationToCopy,
            $newParentLocation
        );
        /* END: Use Case */
    }

    /**
     * Test for the moveSubtree() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LocationService::moveSubtree()
     * @depends Ibexa\Tests\Integration\Core\Repository\LocationServiceTest::testMoveSubtree
     */
    public function testMoveSubtreeThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $mediaLocationId = $this->generateId('location', 43);
        $demoDesignLocationId = $this->generateId('location', 56);
        /* BEGIN: Use Case */
        $user = $this->createMediaUserVersion1();

        // $mediaLocationId is the ID of the "Media" page location in
        // an eZ Publish demo installation

        // $demoDesignLocationId is the ID of the "Demo Design" page location in an eZ
        // Publish demo installation

        // Load the location service
        $locationService = $repository->getLocationService();

        // Load location to move
        $locationToMove = $locationService->loadLocation($mediaLocationId);

        // Load new parent location
        $newParentLocation = $locationService->loadLocation($demoDesignLocationId);

        // Set media editor as current user
        $permissionResolver->setCurrentUserReference($user);

        // This call will fail with an "UnauthorizedException"
        $locationService->moveSubtree(
            $locationToMove,
            $newParentLocation
        );
        /* END: Use Case */
    }
}

class_alias(LocationServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\LocationServiceAuthorizationTest');
