<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\UserServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\User\AssignUserToUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeAssignUserToUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeCreateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeCreateUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeDeleteUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeDeleteUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeMoveUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeUnAssignUserFromUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeUpdateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeUpdateUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeUpdateUserPasswordEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeUpdateUserTokenEvent;
use Ibexa\Contracts\Core\Repository\Events\User\CreateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\CreateUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\MoveUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UnAssignUserFromUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserPasswordEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserTokenEvent;
use Ibexa\Contracts\Core\Repository\UserService as UserServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserTokenUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserService extends UserServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        UserServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createUserGroup(
        UserGroupCreateStruct $userGroupCreateStruct,
        UserGroup $parentGroup
    ): UserGroup {
        $eventData = [
            $userGroupCreateStruct,
            $parentGroup,
        ];

        $beforeEvent = new BeforeCreateUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUserGroup();
        }

        $userGroup = $beforeEvent->hasUserGroup()
            ? $beforeEvent->getUserGroup()
            : $this->innerService->createUserGroup($userGroupCreateStruct, $parentGroup);

        $this->eventDispatcher->dispatch(
            new CreateUserGroupEvent($userGroup, ...$eventData)
        );

        return $userGroup;
    }

    public function deleteUserGroup(UserGroup $userGroup): iterable
    {
        $eventData = [$userGroup];

        $beforeEvent = new BeforeDeleteUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getLocations();
        }

        $locations = $beforeEvent->hasLocations()
            ? $beforeEvent->getLocations()
            : $this->innerService->deleteUserGroup($userGroup);

        $this->eventDispatcher->dispatch(
            new DeleteUserGroupEvent($locations, ...$eventData)
        );

        return $locations;
    }

    public function moveUserGroup(
        UserGroup $userGroup,
        UserGroup $newParent
    ): void {
        $eventData = [
            $userGroup,
            $newParent,
        ];

        $beforeEvent = new BeforeMoveUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->moveUserGroup($userGroup, $newParent);

        $this->eventDispatcher->dispatch(
            new MoveUserGroupEvent(...$eventData)
        );
    }

    public function updateUserGroup(
        UserGroup $userGroup,
        UserGroupUpdateStruct $userGroupUpdateStruct
    ): UserGroup {
        $eventData = [
            $userGroup,
            $userGroupUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedUserGroup();
        }

        $updatedUserGroup = $beforeEvent->hasUpdatedUserGroup()
            ? $beforeEvent->getUpdatedUserGroup()
            : $this->innerService->updateUserGroup($userGroup, $userGroupUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateUserGroupEvent($updatedUserGroup, ...$eventData)
        );

        return $updatedUserGroup;
    }

    public function createUser(
        UserCreateStruct $userCreateStruct,
        array $parentGroups
    ): User {
        $eventData = [
            $userCreateStruct,
            $parentGroups,
        ];

        $beforeEvent = new BeforeCreateUserEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUser();
        }

        $user = $beforeEvent->hasUser()
            ? $beforeEvent->getUser()
            : $this->innerService->createUser($userCreateStruct, $parentGroups);

        $this->eventDispatcher->dispatch(
            new CreateUserEvent($user, ...$eventData)
        );

        return $user;
    }

    public function deleteUser(User $user): iterable
    {
        $eventData = [$user];

        $beforeEvent = new BeforeDeleteUserEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getLocations();
        }

        $locations = $beforeEvent->hasLocations()
            ? $beforeEvent->getLocations()
            : $this->innerService->deleteUser($user);

        $this->eventDispatcher->dispatch(
            new DeleteUserEvent($locations, ...$eventData)
        );

        return $locations;
    }

    public function updateUser(
        User $user,
        UserUpdateStruct $userUpdateStruct
    ): User {
        $eventData = [
            $user,
            $userUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateUserEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedUser();
        }

        $updatedUser = $beforeEvent->hasUpdatedUser()
            ? $beforeEvent->getUpdatedUser()
            : $this->innerService->updateUser($user, $userUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateUserEvent($updatedUser, ...$eventData)
        );

        return $updatedUser;
    }

    public function updateUserPassword(
        User $user,
        string $newPassword
    ): User {
        $eventData = [
            $user,
            new UserUpdateStruct([
                'password' => $newPassword,
            ]),
        ];

        /**
         * @deprecated since eZ Platform by Ibexa v3.1. listening on BeforeUpdateUserEvent when updating password has been deprecated. Use BeforeUpdateUserPasswordEvent instead.
         */
        $beforeEvent = new BeforeUpdateUserEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedUser();
        }

        $beforePasswordEvent = new BeforeUpdateUserPasswordEvent($user, $newPassword);

        $this->eventDispatcher->dispatch($beforePasswordEvent);
        if ($beforePasswordEvent->isPropagationStopped()) {
            return $beforePasswordEvent->getUpdatedUser();
        }

        if ($beforeEvent->hasUpdatedUser()) {
            $updatedUser = $beforeEvent->getUpdatedUser();
        } elseif ($beforePasswordEvent->hasUpdatedUser()) {
            $updatedUser = $beforePasswordEvent->getUpdatedUser();
        } else {
            $updatedUser = $this->innerService->updateUserPassword($user, $newPassword);
        }

        /**
         * @deprecated since eZ Platform by Ibexa v3.1. Listening on UpdateUserEvent when updating password has been deprecated. Use UpdateUserPasswordEvent instead.
         */
        $afterEvent = new UpdateUserEvent($updatedUser, ...$eventData);
        $this->eventDispatcher->dispatch(
            $afterEvent
        );

        $afterPasswordEvent = new UpdateUserPasswordEvent($updatedUser, $user, $newPassword);
        $this->eventDispatcher->dispatch(
            $afterPasswordEvent
        );

        return $updatedUser;
    }

    public function updateUserToken(
        User $user,
        UserTokenUpdateStruct $userTokenUpdateStruct
    ): User {
        $eventData = [
            $user,
            $userTokenUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateUserTokenEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedUser();
        }

        $updatedUser = $beforeEvent->hasUpdatedUser()
            ? $beforeEvent->getUpdatedUser()
            : $this->innerService->updateUserToken($user, $userTokenUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateUserTokenEvent($updatedUser, ...$eventData)
        );

        return $updatedUser;
    }

    public function assignUserToUserGroup(
        User $user,
        UserGroup $userGroup
    ): void {
        $eventData = [
            $user,
            $userGroup,
        ];

        $beforeEvent = new BeforeAssignUserToUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignUserToUserGroup($user, $userGroup);

        $this->eventDispatcher->dispatch(
            new AssignUserToUserGroupEvent(...$eventData)
        );
    }

    public function unAssignUserFromUserGroup(
        User $user,
        UserGroup $userGroup
    ): void {
        $eventData = [
            $user,
            $userGroup,
        ];

        $beforeEvent = new BeforeUnAssignUserFromUserGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->unAssignUserFromUserGroup($user, $userGroup);

        $this->eventDispatcher->dispatch(
            new UnAssignUserFromUserGroupEvent(...$eventData)
        );
    }
}

class_alias(UserService::class, 'eZ\Publish\Core\Event\UserService');
