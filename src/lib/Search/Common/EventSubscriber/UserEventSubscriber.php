<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\User\CreateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\CreateUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\MoveUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserGroupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreateUserEvent::class => 'onCreateUser',
            CreateUserGroupEvent::class => 'onCreateUserGroup',
            DeleteUserEvent::class => 'onDeleteUser',
            DeleteUserGroupEvent::class => 'onDeleteUserGroup',
            MoveUserGroupEvent::class => 'onMoveUserGroup',
            UpdateUserEvent::class => 'onUpdateUser',
            UpdateUserGroupEvent::class => 'onUpdateUserGroup',
        ];
    }

    public function onCreateUser(CreateUserEvent $event)
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUser()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onCreateUserGroup(CreateUserGroupEvent $event)
    {
        $userGroupContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUserGroup()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userGroupContentInfo->id,
                $userGroupContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userGroupContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onDeleteUser(DeleteUserEvent $event)
    {
        $this->searchHandler->deleteContent($event->getUser()->id);

        foreach ($event->getLocations() as $locationId) {
            $this->searchHandler->deleteLocation($locationId, $event->getUser()->id);
        }
    }

    public function onDeleteUserGroup(DeleteUserGroupEvent $event)
    {
        $this->searchHandler->deleteContent($event->getUserGroup()->id);

        foreach ($event->getLocations() as $locationId) {
            $this->searchHandler->deleteLocation($locationId, $event->getUserGroup()->id);
        }
    }

    public function onMoveUserGroup(MoveUserGroupEvent $event)
    {
        $userGroupContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUserGroup()->id
        );

        $this->indexSubtree($userGroupContentInfo->mainLocationId);
    }

    public function onUpdateUser(UpdateUserEvent $event)
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUser()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onUpdateUserGroup(UpdateUserGroupEvent $event)
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUserGroup()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}

class_alias(UserEventSubscriber::class, 'eZ\Publish\Core\Search\Common\EventSubscriber\UserEventSubscriber');
