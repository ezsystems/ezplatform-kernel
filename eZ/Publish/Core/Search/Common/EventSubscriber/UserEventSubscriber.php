<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\EventSubscriber;

use eZ\Publish\API\Repository\Events\User\AssignUserToUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\BeforeUnAssignUserFromUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\CreateUserEvent;
use eZ\Publish\API\Repository\Events\User\CreateUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\DeleteUserEvent;
use eZ\Publish\API\Repository\Events\User\DeleteUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\MoveUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\UnAssignUserFromUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\UpdateUserEvent;
use eZ\Publish\API\Repository\Events\User\UpdateUserGroupEvent;
use eZ\Publish\SPI\Repository\Event\AfterEvent;
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
            AssignUserToUserGroupEvent::class => 'onAssignUserToUserGroup',
            UnAssignUserFromUserGroupEvent::class => 'onUnAssignUserFromUserGroup',
            BeforeUnAssignUserFromUserGroupEvent::class => 'onBeforeUnAssignUserFromUserGroup',
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

    public function onAssignUserToUserGroup(AssignUserToUserGroupEvent $event): void
    {
        $this->indexUserContentWithLocation($event);
    }

    public function onUnAssignUserFromUserGroup(UnAssignUserFromUserGroupEvent $event): void
    {
        $this->indexUserContentWithLocation($event);
    }

    public function onBeforeUnAssignUserFromUserGroup(BeforeUnAssignUserFromUserGroupEvent $event): void
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUser()->id
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->deleteLocation($location->id, $userContentInfo->id);
        }
    }

    private function indexUserContentWithLocation(AfterEvent $event): void
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUser()->id
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
