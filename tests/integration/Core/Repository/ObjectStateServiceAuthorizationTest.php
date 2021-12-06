<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;

/**
 * Test case for operations in the ObjectStateService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService
 * @group integration
 * @group authorization
 */
class ObjectStateServiceAuthorizationTest extends BaseTest
{
    /**
     * Test for the createObjectStateGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::createObjectStateGroup()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testCreateObjectStateGroup
     */
    public function testCreateObjectStateGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        $objectStateService = $repository->getObjectStateService();

        $objectStateGroupCreate = $objectStateService->newObjectStateGroupCreateStruct(
            'publishing'
        );
        $objectStateGroupCreate->defaultLanguageCode = 'eng-US';
        $objectStateGroupCreate->names = [
            'eng-US' => 'Publishing',
            'eng-GB' => 'Sindelfingen',
        ];
        $objectStateGroupCreate->descriptions = [
            'eng-US' => 'Put something online',
            'eng-GB' => 'Put something ton Sindelfingen.',
        ];

        // Throws unauthorized exception, since the anonymous user must not
        // create object state groups
        $createdObjectStateGroup = $objectStateService->createObjectStateGroup(
            $objectStateGroupCreate
        );
        /* END: Use Case */
    }

    /**
     * Test for the updateObjectStateGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::updateObjectStateGroup()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testUpdateObjectStateGroup
     */
    public function testUpdateObjectStateGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $objectStateGroupId = $this->generateId('objectstategroup', 2);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $objectStateGroupId contains the ID of the standard object state
        // group ez_lock.
        $objectStateService = $repository->getObjectStateService();

        $loadedObjectStateGroup = $objectStateService->loadObjectStateGroup(
            $objectStateGroupId
        );

        $groupUpdateStruct = $objectStateService->newObjectStateGroupUpdateStruct();
        $groupUpdateStruct->identifier = 'sindelfingen';
        $groupUpdateStruct->defaultLanguageCode = 'ger-DE';
        $groupUpdateStruct->names = [
            'ger-DE' => 'Sindelfingen',
        ];
        $groupUpdateStruct->descriptions = [
            'ger-DE' => 'Sindelfingen ist nicht nur eine Stadt',
        ];

        // Throws unauthorized exception, since the anonymous user must not
        // update object state groups
        $updatedObjectStateGroup = $objectStateService->updateObjectStateGroup(
            $loadedObjectStateGroup,
            $groupUpdateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the deleteObjectStateGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::deleteObjectStateGroup()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testDeleteObjectStateGroup
     */
    public function testDeleteObjectStateGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $objectStateGroupId = $this->generateId('objectstategroup', 2);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $objectStateGroupId contains the ID of the standard object state
        // group ez_lock.
        $objectStateService = $repository->getObjectStateService();

        $loadedObjectStateGroup = $objectStateService->loadObjectStateGroup(
            $objectStateGroupId
        );

        // Throws unauthorized exception, since the anonymous user must not
        // delete object state groups
        $objectStateService->deleteObjectStateGroup($loadedObjectStateGroup);
        /* END: Use Case */
    }

    /**
     * Test for the createObjectState() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::createObjectState()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testCreateObjectState
     */
    public function testCreateObjectStateThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $objectStateGroupId = $this->generateId('objectstategroup', 2);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $objectStateGroupId contains the ID of the standard object state
        // group ez_lock.
        $objectStateService = $repository->getObjectStateService();

        $loadedObjectStateGroup = $objectStateService->loadObjectStateGroup(
            $objectStateGroupId
        );

        $objectStateCreateStruct = $objectStateService->newObjectStateCreateStruct(
            'locked_and_unlocked'
        );
        $objectStateCreateStruct->priority = 23;
        $objectStateCreateStruct->defaultLanguageCode = 'eng-US';
        $objectStateCreateStruct->names = [
            'eng-US' => 'Locked and Unlocked',
        ];
        $objectStateCreateStruct->descriptions = [
            'eng-US' => 'A state between locked and unlocked.',
        ];

        // Throws unauthorized exception, since the anonymous user must not
        // create object states
        $createdObjectState = $objectStateService->createObjectState(
            $loadedObjectStateGroup,
            $objectStateCreateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the updateObjectState() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::updateObjectState()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testUpdateObjectState
     */
    public function testUpdateObjectStateThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $objectStateId = $this->generateId('objectstate', 2);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $objectStateId contains the ID of the "locked" state
        $objectStateService = $repository->getObjectStateService();

        $loadedObjectState = $objectStateService->loadObjectState(
            $objectStateId
        );

        $updateStateStruct = $objectStateService->newObjectStateUpdateStruct();
        $updateStateStruct->identifier = 'somehow_locked';
        $updateStateStruct->defaultLanguageCode = 'ger-DE';
        $updateStateStruct->names = [
            'eng-US' => 'Somehow locked',
            'ger-DE' => 'Irgendwie gelockt',
        ];
        $updateStateStruct->descriptions = [
            'eng-US' => 'The object is somehow locked',
            'ger-DE' => 'Sindelfingen',
        ];

        // Throws unauthorized exception, since the anonymous user must not
        // update object states
        $updatedObjectState = $objectStateService->updateObjectState(
            $loadedObjectState,
            $updateStateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the setPriorityOfObjectState() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::setPriorityOfObjectState()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testSetPriorityOfObjectState
     */
    public function testSetPriorityOfObjectStateThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $objectStateId = $this->generateId('objectstate', 2);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $objectStateId contains the ID of the "locked" state
        $objectStateService = $repository->getObjectStateService();

        $initiallyLoadedObjectState = $objectStateService->loadObjectState(
            $objectStateId
        );

        // Throws unauthorized exception, since the anonymous user must not
        // set priorities for object states
        $objectStateService->setPriorityOfObjectState(
            $initiallyLoadedObjectState,
            23
        );
        /* END: Use Case */
    }

    /**
     * Test for the deleteObjectState() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::deleteObjectState()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testDeleteObjectState
     */
    public function testDeleteObjectStateThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $notLockedObjectStateId = $this->generateId('objectstate', 1);
        $lockedObjectStateId = $this->generateId('objectstate', 2);
        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $notLockedObjectStateId is the ID of the state "not_locked"
        $objectStateService = $repository->getObjectStateService();

        $notLockedObjectState = $objectStateService->loadObjectState($notLockedObjectStateId);

        // Throws unauthorized exception, since the anonymous user must not
        // delete object states
        $objectStateService->deleteObjectState($notLockedObjectState);
        /* END: Use Case */
    }

    /**
     * Test for the setContentState() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ObjectStateService::setContentState()
     * @depends Ibexa\Tests\Integration\Core\Repository\ObjectStateServiceTest::testSetContentState
     */
    public function testSetContentStateThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        $ezLockObjectStateGroupId = $this->generateId('objectstategroup', 2);
        $lockedObjectStateId = $this->generateId('objectstate', 2);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // Set anonymous user
        $userService = $repository->getUserService();
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // $anonymousUserId is the content ID of "Anonymous User"
        // $ezLockObjectStateGroupId contains the ID of the "ez_lock" object
        // state group
        // $lockedObjectStateId is the ID of the state "locked"
        $contentService = $repository->getContentService();
        $objectStateService = $repository->getObjectStateService();

        $contentInfo = $contentService->loadContentInfo($anonymousUserId);

        $ezLockObjectStateGroup = $objectStateService->loadObjectStateGroup(
            $ezLockObjectStateGroupId
        );
        $lockedObjectState = $objectStateService->loadObjectState($lockedObjectStateId);

        // Throws unauthorized exception, since the anonymous user must not
        // set object state
        $objectStateService->setContentState(
            $contentInfo,
            $ezLockObjectStateGroup,
            $lockedObjectState
        );
        /* END: Use Case */
    }
}

class_alias(ObjectStateServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\ObjectStateServiceAuthorizationTest');
