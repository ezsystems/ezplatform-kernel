<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use function array_filter;
use Ibexa\Contracts\Core\Limitation\Target\Builder\VersionBuilder;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\LookupLimitationResult;
use Ibexa\Contracts\Core\Repository\Values\User\LookupPolicyLimitations;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Repository\Values\User\UserReference;

/**
 *  Test case for operations in the PermissionResolver.
 *
 * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver
 * @group integration
 * @group permission
 */
class PermissionResolverTest extends BaseTest
{
    /**
     * Test for the getCurrentUserReference() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::getCurrentUserReference()
     */
    public function testGetCurrentUserReferenceReturnsAnonymousUserReference()
    {
        $repository = $this->getRepository();
        $anonymousUserId = $this->generateId('user', 10);
        $repository->getPermissionResolver()->setCurrentUserReference(
            new UserReference($anonymousUserId)
        );

        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Only a UserReference has previously been set to the $repository

        $permissionResolver = $repository->getPermissionResolver();
        $anonymousUserReference = $permissionResolver->getCurrentUserReference();
        /* END: Use Case */

        self::assertEquals(
            $anonymousUserReference->getUserId(),
            $repository->getUserService()->loadUser($anonymousUserId)->id
        );
    }

    /**
     * Test for the setCurrentUserReference() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::setCurrentUserReference()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     */
    public function testSetCurrentUserReference()
    {
        $repository = $this->getRepository();
        $repository->getPermissionResolver()->setCurrentUserReference(
            new UserReference(
                $this->generateId('user', 10)
            )
        );

        $administratorUserId = $this->generateId('user', 14);

        /* BEGIN: Use Case */
        // $administratorUserId contains the ID of the administrator user

        $permissionResolver = $repository->getPermissionResolver();

        $userService = $repository->getUserService();

        // Load administrator user
        $administratorUser = $userService->loadUser($administratorUserId);

        // Set administrator user as current user reference
        $permissionResolver->setCurrentUserReference($administratorUser);
        /* END: Use Case */

        $this->assertEquals(
            $administratorUserId,
            $permissionResolver->getCurrentUserReference()->getUserId()
        );

        $this->assertSame(
            $administratorUser,
            $permissionResolver->getCurrentUserReference()
        );
    }

    /**
     * Test for the hasAccess() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::hasAccess()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     */
    public function testHasAccessWithAnonymousUserNo()
    {
        $repository = $this->getRepository();

        $anonymousUserId = $this->generateId('user', 10);

        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.

        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load anonymous user
        $anonymousUser = $userService->loadUser($anonymousUserId);

        // This call will return false because anonymous user does not have access
        // to content removal
        $hasAccess = $permissionResolver->hasAccess('content', 'remove', $anonymousUser);
        /* END: Use Case */

        $this->assertFalse($hasAccess);
    }

    /**
     * Test for the hasAccess() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::hasAccess()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends testHasAccessWithAnonymousUserNo
     */
    public function testHasAccessForCurrentUserNo()
    {
        $repository = $this->getRepository();

        $anonymousUserId = $this->generateId('user', 10);

        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.

        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load anonymous user
        $anonymousUser = $userService->loadUser($anonymousUserId);

        // Set anonymous user as current user reference
        $permissionResolver->setCurrentUserReference($anonymousUser);

        // This call will return false because anonymous user does not have access
        // to content removal
        $hasAccess = $permissionResolver->hasAccess('content', 'remove');
        /* END: Use Case */

        $this->assertFalse($hasAccess);
    }

    /**
     * Test for the hasAccess() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::hasAccess()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     */
    public function testHasAccessWithAdministratorUser()
    {
        $repository = $this->getRepository();

        $administratorUserId = $this->generateId('user', 14);

        /* BEGIN: Use Case */
        // $administratorUserId contains the ID of the administrator user

        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load administrator user
        $administratorUser = $userService->loadUser($administratorUserId);

        // This call will return true
        $hasAccess = $permissionResolver->hasAccess('content', 'read', $administratorUser);
        /* END: Use Case */

        $this->assertTrue($hasAccess);
    }

    /**
     * Test for the hasAccess() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::hasAccess()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends testSetCurrentUserReference
     * @depends testHasAccessWithAdministratorUser
     */
    public function testHasAccessForCurrentUserYes()
    {
        $repository = $this->getRepository();

        $administratorUserId = $this->generateId('user', 14);

        /* BEGIN: Use Case */
        // $administratorUserId contains the ID of the administrator user

        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load administrator user
        $administratorUser = $userService->loadUser($administratorUserId);

        // Set administrator user as current user reference
        $permissionResolver->setCurrentUserReference($administratorUser);

        // This call will return true
        $hasAccess = $permissionResolver->hasAccess('content', 'read');
        /* END: Use Case */

        $this->assertTrue($hasAccess);
    }

    /**
     * Test for the hasAccess() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::hasAccess()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends testSetCurrentUserReference
     */
    public function testHasAccessLimited()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        // This call will return an array of permission sets describing user's access
        // to reading content
        $permissionSets = $permissionResolver->hasAccess('content', 'read');
        /* END: Use Case */

        $this->assertIsArray(
            $permissionSets
        );
        $this->assertNotEmpty($permissionSets);
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends testHasAccessForCurrentUserNo
     */
    public function testCanUserForAnonymousUserNo()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $homeId = $this->generateId('object', 57);

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // $homeId contains the ID of the "Home" frontpage

        $contentService = $repository->getContentService();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load anonymous user
        $anonymousUser = $userService->loadUser($anonymousUserId);

        // Set anonymous user as current user reference
        $permissionResolver->setCurrentUserReference($anonymousUser);

        // Load the ContentInfo for "Home" frontpage
        $contentInfo = $contentService->loadContentInfo($homeId);

        // This call will return false because anonymous user does not have access
        // to content removal and hence no permission to remove given content
        $canUser = $permissionResolver->canUser('content', 'remove', $contentInfo);

        // Performing an action without necessary permissions will fail with "UnauthorizedException"
        if (!$canUser) {
            $contentService->deleteContent($contentInfo);
        }
        /* END: Use Case */
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends testHasAccessForCurrentUserYes
     */
    public function testCanUserForAdministratorUser()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        $administratorUserId = $this->generateId('user', 14);
        $homeId = $this->generateId('object', 57);

        /* BEGIN: Use Case */
        // $administratorUserId contains the ID of the administrator user
        // $homeId contains the ID of the "Home" frontpage

        $contentService = $repository->getContentService();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load administrator user
        $administratorUser = $userService->loadUser($administratorUserId);

        // Set administrator user as current user reference
        $permissionResolver->setCurrentUserReference($administratorUser);

        // Load the ContentInfo for "Home" frontpage
        $contentInfo = $contentService->loadContentInfo($homeId);

        // This call will return true
        $canUser = $permissionResolver->canUser('content', 'remove', $contentInfo);

        // Performing an action having necessary permissions will succeed
        $contentService->deleteContent($contentInfo);
        /* END: Use Case */

        $this->assertTrue($canUser);
        $contentService->loadContent($homeId);
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends testHasAccessLimited
     */
    public function testCanUserWithLimitationYes()
    {
        $repository = $this->getRepository();

        $imagesFolderId = $this->generateId('object', 49);

        /* BEGIN: Use Case */
        // $imagesFolderId contains the ID of the "Images" folder

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentService = $repository->getContentService();

        // Performing an action having necessary permissions will succeed
        $imagesFolder = $contentService->loadContent($imagesFolderId);

        // This call will return true
        $canUser = $permissionResolver->canUser('content', 'read', $imagesFolder);
        /* END: Use Case */

        $this->assertTrue($canUser);
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends testHasAccessLimited
     */
    public function testCanUserWithLimitationNo()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $administratorUserId = $this->generateId('user', 14);

        /* BEGIN: Use Case */
        // $administratorUserId contains the ID of the administrator user

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $userService = $repository->getUserService();

        // Load administrator user using UserService, this does not check for permissions
        $administratorUser = $userService->loadUser($administratorUserId);

        // This call will return false as user with Editor role does not have
        // permission to read "Users" subtree
        $canUser = $permissionResolver->canUser('content', 'read', $administratorUser);

        $contentService = $repository->getContentService();

        // Performing an action without necessary permissions will fail with "UnauthorizedException"
        if (!$canUser) {
            $content = $contentService->loadContent($administratorUserId);
        }
        /* END: Use Case */
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentTypeService
     * @depends testSetCurrentUserReference
     * @depends testHasAccessLimited
     */
    public function testCanUserThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        $userGroupContentTypeId = $this->generateId('type', 3);

        /* BEGIN: Use Case */
        // $userGroupContentTypeId contains the ID of the "UserGroup" ContentType

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        // Load the "UserGroup" ContentType
        $userGroupContentType = $contentTypeService->loadContentType($userGroupContentTypeId);

        // This call will throw "InvalidArgumentException" because $userGroupContentType
        // is an instance of \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType,
        // which can not be checked for user access
        $permissionResolver->canUser('content', 'create', $userGroupContentType);
        /* END: Use Case */
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentTypeService
     * @depends testHasAccessLimited
     */
    public function testCanUserWithTargetYes()
    {
        $repository = $this->getRepository();

        $homeLocationId = $this->generateId('location', 2);

        /* BEGIN: Use Case */
        // $homeLocationId contains the ID of the "Home" location

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('forums');

        $contentService = $repository->getContentService();

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->setField('title', 'My awesome forums');
        $contentCreateStruct->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreateStruct->alwaysAvailable = true;

        $locationService = $repository->getLocationService();
        $locationCreateStruct = $locationService->newLocationCreateStruct($homeLocationId);

        // This call will return true
        $canUser = $permissionResolver->canUser(
            'content',
            'create',
            $contentCreateStruct,
            [$locationCreateStruct]
        );

        // Performing an action having necessary permissions will succeed
        $contentDraft = $contentService->createContent(
            $contentCreateStruct,
            [$locationCreateStruct]
        );
        /* END: Use Case */

        $this->assertTrue($canUser);
        $this->assertEquals(
            'My awesome forums',
            $contentDraft->getFieldValue('title')->text
        );
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentTypeService
     * @depends testHasAccessLimited
     */
    public function testCanUserWithTargetNo()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $homeLocationId = $this->generateId('location', 2);

        /* BEGIN: Use Case */
        // $homeLocationId contains the ID of the "Home" frontpage location

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('forum');

        $contentService = $repository->getContentService();

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->setField('name', 'My awesome forum');
        $contentCreateStruct->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreateStruct->alwaysAvailable = true;

        $locationService = $repository->getLocationService();
        $locationCreateStruct = $locationService->newLocationCreateStruct($homeLocationId);

        // This call will return false because user with Editor role has permission to
        // create "forum" type content only under "folder" type content.
        $canUser = $permissionResolver->canUser(
            'content',
            'create',
            $contentCreateStruct,
            [$locationCreateStruct]
        );

        // Performing an action without necessary permissions will fail with "UnauthorizedException"
        if (!$canUser) {
            $contentDraft = $contentService->createContent(
                $contentCreateStruct,
                [$locationCreateStruct]
            );
        }
        /* END: Use Case */
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentTypeService
     * @depends testHasAccessLimited
     */
    public function testCanUserWithMultipleTargetsYes()
    {
        $repository = $this->getRepository();

        $imagesLocationId = $this->generateId('location', 51);
        $filesLocationId = $this->generateId('location', 52);

        /* BEGIN: Use Case */
        // $imagesLocationId contains the ID of the "Images" location
        // $filesLocationId contains the ID of the "Files" location

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('folder');

        $contentService = $repository->getContentService();

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->setField('name', 'My multipurpose folder');
        $contentCreateStruct->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreateStruct->alwaysAvailable = true;

        $locationService = $repository->getLocationService();
        $locationCreateStruct1 = $locationService->newLocationCreateStruct($imagesLocationId);
        $locationCreateStruct2 = $locationService->newLocationCreateStruct($filesLocationId);
        $locationCreateStructs = [$locationCreateStruct1, $locationCreateStruct2];

        // This call will return true
        $canUser = $permissionResolver->canUser(
            'content',
            'create',
            $contentCreateStruct,
            $locationCreateStructs
        );

        // Performing an action having necessary permissions will succeed
        $contentDraft = $contentService->createContent($contentCreateStruct, $locationCreateStructs);
        /* END: Use Case */

        $this->assertTrue($canUser);
        $this->assertEquals(
            'My multipurpose folder',
            $contentDraft->getFieldValue('name')->text
        );
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentTypeService
     * @depends testHasAccessLimited
     */
    public function testCanUserWithMultipleTargetsNo()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $homeLocationId = $this->generateId('location', 2);
        $administratorUsersLocationId = $this->generateId('location', 13);

        /* BEGIN: Use Case */
        // $homeLocationId contains the ID of the "Home" location
        // $administratorUsersLocationId contains the ID of the "Administrator users" location

        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('forums');

        $contentService = $repository->getContentService();

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->setField('title', 'My awesome forums');
        $contentCreateStruct->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreateStruct->alwaysAvailable = true;

        $locationService = $repository->getLocationService();
        $locationCreateStruct1 = $locationService->newLocationCreateStruct($homeLocationId);
        $locationCreateStruct2 = $locationService->newLocationCreateStruct($administratorUsersLocationId);
        $locationCreateStructs = [$locationCreateStruct1, $locationCreateStruct2];

        // This call will return false because user with Editor role does not have permission to
        // create content in the "Administrator users" location subtree
        $canUser = $permissionResolver->canUser(
            'content',
            'create',
            $contentCreateStruct,
            $locationCreateStructs
        );

        // Performing an action without necessary permissions will fail with "UnauthorizedException"
        if (!$canUser) {
            $contentDraft = $contentService->createContent($contentCreateStruct, $locationCreateStructs);
        }
        /* END: Use Case */
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentTypeService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetURLAliasService
     * @depends testSetCurrentUserReference
     * @depends testHasAccessLimited
     */
    public function testCanUserWithTargetThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $permissionResolver = $repository->getPermissionResolver();

        // Set created user as current user reference
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('forum');

        $contentService = $repository->getContentService();

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->setField('name', 'My awesome forum');
        $contentCreateStruct->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreateStruct->alwaysAvailable = true;

        $urlAliasService = $repository->getURLAliasService();
        $rootUrlAlias = $urlAliasService->lookup('/');

        // This call will throw "InvalidArgumentException" because $rootAlias is not a valid target object
        $canUser = $permissionResolver->canUser(
            'content',
            'create',
            $contentCreateStruct,
            [$rootUrlAlias]
        );
        /* END: Use Case */
    }

    /**
     * Test for the canUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     */
    public function testCanUserThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $this->markTestIncomplete(
            'Cannot be tested on current fixture since policy with unsupported limitation value is not available.'
        );
    }

    /**
     * Test PermissionResolver::canUser for Users with different Limitations.
     *
     * @covers       \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser
     *
     * @dataProvider getDataForTestCanUserWithLimitations
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     * @param string $module
     * @param string $function
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $object
     * @param array $targets
     * @param bool $expectedResult expected result of canUser check
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testCanUserWithLimitations(
        Limitation $limitation,
        $module,
        $function,
        ValueObject $object,
        array $targets,
        $expectedResult
    ) {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $roleService = $repository->getRoleService();
        $permissionResolver = $repository->getPermissionResolver();

        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                ['module' => $module, 'function' => $function, 'limitations' => [$limitation]],
            ]
        );
        // create user in root user group to avoid overlapping of existing policies and limitations
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        $roleLimitation = $limitation instanceof Limitation\RoleLimitation ? $limitation : null;
        $roleService->assignRoleToUser($role, $user, $roleLimitation);

        $permissionResolver->setCurrentUserReference($user);

        self::assertEquals(
            $expectedResult,
            $permissionResolver->canUser($module, $function, $object, $targets)
        );
    }

    /**
     * Data provider for testCanUserWithLimitations.
     *
     * @see testCanUserWithLimitations
     *
     * @return array
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getDataForTestCanUserWithLimitations()
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('folder');

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-US');
        $contentCreateStruct->sectionId = 2;

        // return data sets, numbered for readability and debugging
        return [
            0 => [
                new Limitation\SubtreeLimitation(['limitationValues' => ['/1/2/']]),
                'content',
                'create',
                $contentCreateStruct,
                [],
                false,
            ],
            1 => [
                new Limitation\SectionLimitation(['limitationValues' => [2]]),
                'content',
                'create',
                $contentCreateStruct,
                [],
                true,
            ],
            2 => [
                new Limitation\ParentContentTypeLimitation(['limitationValues' => [1]]),
                'content',
                'create',
                $contentCreateStruct,
                [],
                false,
            ],
            3 => [
                new Limitation\ParentContentTypeLimitation(['limitationValues' => [3]]), // parent type has to be the UserGroup
                'content',
                'create',
                $contentService->loadContentInfo(14), // content type user (Administrator)
                [],
                true,
            ],
        ];
    }

    /**
     * Test for the lookupLimitations() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::lookupLimitations()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\PermissionResolverTest::testHasAccessForCurrentUserNo
     */
    public function testLookupLimitationsForAnonymousUserHasNoAccess(): void
    {
        $repository = $this->getRepository();

        $homeId = $this->generateId('object', 57);

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // $homeId contains the ID of the "Home" frontpage

        $contentService = $repository->getContentService();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load anonymous user
        $anonymousUser = $userService->loadUser($anonymousUserId);

        // Set anonymous user as current user reference
        $permissionResolver->setCurrentUserReference($anonymousUser);

        // Load the ContentInfo for "Home" frontpage
        $contentInfo = $contentService->loadContentInfo($homeId);

        // `$lookupLimitations->hasAccess` will return false because anonymous user does not have access
        // to content removal and hence no permission to remove given content. `$lookupLimitations->lookupPolicyLimitations`
        // will be empty array
        $lookupLimitations = $permissionResolver->lookupLimitations('content', 'remove', $contentInfo);
        /* END: Use Case */

        $this->assertFalse($lookupLimitations->hasAccess);
        $this->assertEquals($lookupLimitations->roleLimitations, []);
        $this->assertEquals($lookupLimitations->lookupPolicyLimitations, []);
    }

    /**
     * Test for the lookupLimitations() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::lookupLimitations()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\PermissionResolverTest::testHasAccessForCurrentUserYes
     */
    public function testLookupLimitationsForAdministratorUser(): void
    {
        $repository = $this->getRepository();

        $administratorUserId = $this->generateId('user', 14);
        $homeId = $this->generateId('object', 57);

        /* BEGIN: Use Case */
        // $administratorUserId contains the ID of the administrator user
        // $homeId contains the ID of the "Home" frontpage

        $contentService = $repository->getContentService();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();

        // Load administrator user
        $administratorUser = $userService->loadUser($administratorUserId);

        // Set administrator user as current user reference
        $permissionResolver->setCurrentUserReference($administratorUser);

        // Load the ContentInfo for "Home" frontpage
        $contentInfo = $contentService->loadContentInfo($homeId);

        // This call will return true
        $lookupLimitations = $permissionResolver->lookupLimitations('content', 'remove', $contentInfo);
        /* END: Use Case */

        $this->assertTrue($lookupLimitations->hasAccess);
        $this->assertEquals($lookupLimitations->roleLimitations, []);
        $this->assertEquals($lookupLimitations->lookupPolicyLimitations, []);
    }

    /**
     * When one of policy pass then all limitation should be returned.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::lookupLimitations()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\PermissionResolverTest::testHasAccessForCurrentUserYes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testLookupLimitationsWithLimitations(): void
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $roleService = $repository->getRoleService();

        $module = 'content';
        $function = 'create';

        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                ['module' => $module, 'function' => $function, 'limitations' => [new Limitation\SubtreeLimitation(['limitationValues' => ['/1/2/']])]],
                ['module' => $module, 'function' => $function, 'limitations' => [
                    new Limitation\SectionLimitation(['limitationValues' => [2]]),
                    new Limitation\LanguageLimitation(['limitationValues' => ['eng-US']]),
                ]],
                ['module' => 'content', 'function' => 'edit', 'limitations' => [new Limitation\SectionLimitation(['limitationValues' => [2]])]],
            ]
        );
        // create user in root user group to avoid overlapping of existing policies and limitations
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        // Here we have no RoleLimitation
        $roleService->assignRoleToUser($role, $user);
        $permissionResolver->setCurrentUserReference($user);

        $expected = new LookupLimitationResult(
            true,
            [],
            [
                new LookupPolicyLimitations(
                    $role->getPolicies()[1],
                    [
                        new Limitation\SectionLimitation(['limitationValues' => [2]]),
                        new Limitation\LanguageLimitation(['limitationValues' => ['eng-US']]),
                    ]
                ),
            ]
        );

        self::assertEquals(
            $expected,
            $permissionResolver->lookupLimitations($module, $function, $this->getContentCreateStruct($repository), [])
        );
    }

    /**
     * When one of policy pass then only filtered limitation should be returned.
     *
     * @covers \Ibexa\Contracts\Core\Repository\PermissionResolver::lookupLimitations()
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetUserService
     * @depends Ibexa\Tests\Integration\Core\Repository\RepositoryTest::testGetContentService
     * @depends Ibexa\Tests\Integration\Core\Repository\PermissionResolverTest::testHasAccessForCurrentUserYes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testLookupLimitationsWithFilteredLimitations(): void
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $roleService = $repository->getRoleService();

        $module = 'content';
        $function = 'create';

        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                ['module' => $module, 'function' => $function, 'limitations' => [new Limitation\SubtreeLimitation(['limitationValues' => ['/1/2/']])]],
                ['module' => $module, 'function' => $function, 'limitations' => [
                    new Limitation\SectionLimitation(['limitationValues' => [2]]),
                    new Limitation\LanguageLimitation(['limitationValues' => ['eng-US']]),
                ]],
                ['module' => 'content', 'function' => 'edit', 'limitations' => [new Limitation\SectionLimitation(['limitationValues' => [2]])]],
            ]
        );
        // create user in root user group to avoid overlapping of existing policies and limitations
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        // Here we have no RoleLimitation
        $roleService->assignRoleToUser($role, $user);
        $permissionResolver->setCurrentUserReference($user);

        $expected = new LookupLimitationResult(
            true,
            [],
            [
                new LookupPolicyLimitations(
                    $role->getPolicies()[1],
                    [
                        new Limitation\SectionLimitation(['limitationValues' => [2]]),
                    ]
                ),
            ]
        );

        self::assertEquals(
            $expected,
            $permissionResolver->lookupLimitations($module, $function, $this->getContentCreateStruct($repository), [], [Limitation::SECTION])
        );
    }

    /**
     * If the role limitation is set it should be taken into account. In this case, role limitation
     * will pass and ContentTypeLimitation should be returned.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testLookupLimitationsWithRoleLimitationsHasAccess(): void
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $roleService = $repository->getRoleService();

        $module = 'content';
        $function = 'create';

        /* BEGIN: Use Case */
        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                ['module' => $module, 'function' => $function, 'limitations' => [new Limitation\SubtreeLimitation(['limitationValues' => ['/1/2/']])]],
                ['module' => $module, 'function' => $function, 'limitations' => [new Limitation\LanguageLimitation(['limitationValues' => ['eng-US']])]],
                ['module' => 'content', 'function' => 'edit', 'limitations' => [new Limitation\SectionLimitation(['limitationValues' => [2]])]],
            ]
        );
        // create user in root user group to avoid overlapping of existing policies and limitations
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        // SectionLimitation as RoleLimitation will pass
        $roleLimitation = new Limitation\SectionLimitation(['limitationValues' => [2]]);
        $roleService->assignRoleToUser($role, $user, $roleLimitation);
        $permissionResolver->setCurrentUserReference($user);
        /* END: Use Case */

        $expected = new LookupLimitationResult(
            true,
            [$roleLimitation],
            [
                new LookupPolicyLimitations(
                    $role->getPolicies()[1],
                    [new Limitation\LanguageLimitation(['limitationValues' => ['eng-US']])]
                ),
            ]
        );

        self::assertEquals(
            $expected,
            $permissionResolver->lookupLimitations($module, $function, $this->getContentCreateStruct($repository), [])
        );
    }

    /**
     * If the role limitation is set and policy limitation is not set it should be taken into account.
     * In this case, role limitation will pass and SectionLimitation should be returned as role limitation
     * and limitations in LookupPolicyLimitations should be an empty array.
     *
     * @see https://jira.ez.no/browse/EZP-30728
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testLookupLimitationsWithRoleLimitationsWithoutPolicyLimitationsHasAccess(): void
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $roleService = $repository->getRoleService();

        $module = 'content';
        $function = 'create';

        /* BEGIN: Use Case */
        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                ['module' => $module, 'function' => $function, 'limitations' => []],
                ['module' => 'content', 'function' => 'edit', 'limitations' => []],
            ]
        );
        // create user in root user group to avoid overlapping of existing policies and limitations
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        // SectionLimitation as RoleLimitation will pass
        $roleLimitation = new Limitation\SectionLimitation(['limitationValues' => [2]]);
        $roleService->assignRoleToUser($role, $user, $roleLimitation);
        $permissionResolver->setCurrentUserReference($user);
        /* END: Use Case */

        $expectedPolicy = current(array_filter($role->getPolicies(), static function ($policy) use ($module, $function) {
            return $policy->module === $module && $policy->function === $function;
        }));

        $expected = new LookupLimitationResult(
            true,
            [$roleLimitation],
            [
                new LookupPolicyLimitations(
                    $expectedPolicy,
                    []
                ),
            ]
        );

        self::assertEquals(
            $expected,
            $permissionResolver->lookupLimitations($module, $function, $this->getContentCreateStruct($repository), [])
        );
    }

    /**
     * If the role limitation is set it should be taken into account. In this case, role limitation
     * will not pass and ContentTypeLimitation should not be returned.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testLookupLimitationsWithRoleLimitationsHasNoAccess(): void
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $roleService = $repository->getRoleService();

        $module = 'content';
        $function = 'create';

        /* BEGIN: Use Case */
        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                ['module' => $module, 'function' => $function, 'limitations' => [new Limitation\SubtreeLimitation(['limitationValues' => ['/1/2/']])]],
                ['module' => $module, 'function' => $function, 'limitations' => [new Limitation\LanguageLimitation(['limitationValues' => ['eng-US']])]],
                ['module' => 'content', 'function' => 'edit', 'limitations' => [new Limitation\SectionLimitation(['limitationValues' => [2]])]],
            ]
        );
        // create user in root user group to avoid overlapping of existing policies and limitations
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        // SectionLimitation as RoleLimitation will not pass
        $roleLimitation = new Limitation\SectionLimitation(['limitationValues' => [3]]);
        $roleService->assignRoleToUser($role, $user, $roleLimitation);
        $permissionResolver->setCurrentUserReference($user);
        /* END: Use Case */

        $expected = new LookupLimitationResult(
            false,
            [],
            []
        );

        self::assertEquals(
            $expected,
            $permissionResolver->lookupLimitations($module, $function, $this->getContentCreateStruct($repository), [])
        );
    }

    public function testLookupLimitationsWithMixedTargets(): void
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $roleService = $repository->getRoleService();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation(2);
        $module = 'content';
        $function = 'edit';

        $role = $this->createRoleWithPolicies(
            'role_' . __FUNCTION__,
            [
                [
                    'module' => $module,
                    'function' => $function,
                    'limitations' => [
                        new Limitation\LocationLimitation(['limitationValues' => [$location->id]]),
                    ],
                ],
                [
                    'module' => $module,
                    'function' => $function,
                    'limitations' => [
                        new Limitation\LanguageLimitation(['limitationValues' => ['eng-GB']]),
                    ],
                ],
            ]
        );
        $user = $this->createUser('user', 'John', 'Doe', $userService->loadUserGroup(4));
        $roleService->assignRoleToUser($role, $user);
        $permissionResolver->setCurrentUserReference($user);

        $actual = $permissionResolver->lookupLimitations(
            $module,
            $function,
            $location->contentInfo,
            [
                (new VersionBuilder())->translateToAnyLanguageOf(['eng-GB'])->build(),
                $location,
            ],
            [Limitation::LANGUAGE]
        );

        self::assertTrue($actual->hasAccess);
        self::assertEmpty($actual->roleLimitations);
        self::assertCount(2, $actual->lookupPolicyLimitations);

        $firstPolicy = $actual->lookupPolicyLimitations[0]->policy;
        if ($firstPolicy->limitations[0] instanceof Limitation\LanguageLimitation) {
            $lookupPolicyLanguageLimitation = new LookupPolicyLimitations(
                $firstPolicy,
                [
                    new Limitation\LanguageLimitation(['limitationValues' => ['eng-GB']]),
                ]
            );
            $lookupPolicyLocationLimitation = new LookupPolicyLimitations(
                $actual->lookupPolicyLimitations[1]->policy,
                []
            );
        } else {
            $lookupPolicyLanguageLimitation = new LookupPolicyLimitations(
                $actual->lookupPolicyLimitations[1]->policy,
                [
                    new Limitation\LanguageLimitation(['limitationValues' => ['eng-GB']]),
                ]
            );
            $lookupPolicyLocationLimitation = new LookupPolicyLimitations(
                $firstPolicy,
                []
            );
        }

        self::assertTrue(
            in_array(
                $lookupPolicyLanguageLimitation,
                $actual->lookupPolicyLimitations
            )
        );
        self::assertTrue(
            in_array(
                $lookupPolicyLocationLimitation,
                $actual->lookupPolicyLimitations
            )
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param string $contentTypeIdentifier
     * @param string $mainLanguageCode
     * @param int $sectionId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getContentCreateStruct(
        Repository $repository,
        string $contentTypeIdentifier = 'folder',
        string $mainLanguageCode = 'eng-US',
        int $sectionId = 2
    ): ContentCreateStruct {
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, $mainLanguageCode);
        $contentCreateStruct->sectionId = $sectionId;

        return $contentCreateStruct;
    }
}

class_alias(PermissionResolverTest::class, 'eZ\Publish\API\Repository\Tests\PermissionResolverTest');
