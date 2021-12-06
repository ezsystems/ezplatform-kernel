<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\ObjectStateServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeCreateObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeCreateObjectStateGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeDeleteObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeDeleteObjectStateGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeSetContentStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeSetPriorityOfObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeUpdateObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\BeforeUpdateObjectStateGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\CreateObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\CreateObjectStateGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\DeleteObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\DeleteObjectStateGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\SetContentStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\SetPriorityOfObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\UpdateObjectStateEvent;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\UpdateObjectStateGroupEvent;
use Ibexa\Contracts\Core\Repository\ObjectStateService as ObjectStateServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ObjectStateService extends ObjectStateServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        ObjectStateServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createObjectStateGroup(ObjectStateGroupCreateStruct $objectStateGroupCreateStruct): ObjectStateGroup
    {
        $eventData = [$objectStateGroupCreateStruct];

        $beforeEvent = new BeforeCreateObjectStateGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getObjectStateGroup();
        }

        $objectStateGroup = $beforeEvent->hasObjectStateGroup()
            ? $beforeEvent->getObjectStateGroup()
            : $this->innerService->createObjectStateGroup($objectStateGroupCreateStruct);

        $this->eventDispatcher->dispatch(
            new CreateObjectStateGroupEvent($objectStateGroup, ...$eventData)
        );

        return $objectStateGroup;
    }

    public function updateObjectStateGroup(
        ObjectStateGroup $objectStateGroup,
        ObjectStateGroupUpdateStruct $objectStateGroupUpdateStruct
    ): ObjectStateGroup {
        $eventData = [
            $objectStateGroup,
            $objectStateGroupUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateObjectStateGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedObjectStateGroup();
        }

        $updatedObjectStateGroup = $beforeEvent->hasUpdatedObjectStateGroup()
            ? $beforeEvent->getUpdatedObjectStateGroup()
            : $this->innerService->updateObjectStateGroup($objectStateGroup, $objectStateGroupUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateObjectStateGroupEvent($updatedObjectStateGroup, ...$eventData)
        );

        return $updatedObjectStateGroup;
    }

    public function deleteObjectStateGroup(ObjectStateGroup $objectStateGroup): void
    {
        $eventData = [$objectStateGroup];

        $beforeEvent = new BeforeDeleteObjectStateGroupEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteObjectStateGroup($objectStateGroup);

        $this->eventDispatcher->dispatch(
            new DeleteObjectStateGroupEvent(...$eventData)
        );
    }

    public function createObjectState(
        ObjectStateGroup $objectStateGroup,
        ObjectStateCreateStruct $objectStateCreateStruct
    ): ObjectState {
        $eventData = [
            $objectStateGroup,
            $objectStateCreateStruct,
        ];

        $beforeEvent = new BeforeCreateObjectStateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getObjectState();
        }

        $objectState = $beforeEvent->hasObjectState()
            ? $beforeEvent->getObjectState()
            : $this->innerService->createObjectState($objectStateGroup, $objectStateCreateStruct);

        $this->eventDispatcher->dispatch(
            new CreateObjectStateEvent($objectState, ...$eventData)
        );

        return $objectState;
    }

    public function updateObjectState(
        ObjectState $objectState,
        ObjectStateUpdateStruct $objectStateUpdateStruct
    ): ObjectState {
        $eventData = [
            $objectState,
            $objectStateUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateObjectStateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedObjectState();
        }

        $updatedObjectState = $beforeEvent->hasUpdatedObjectState()
            ? $beforeEvent->getUpdatedObjectState()
            : $this->innerService->updateObjectState($objectState, $objectStateUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateObjectStateEvent($updatedObjectState, ...$eventData)
        );

        return $updatedObjectState;
    }

    public function setPriorityOfObjectState(
        ObjectState $objectState,
        int $priority
    ): void {
        $eventData = [
            $objectState,
            $priority,
        ];

        $beforeEvent = new BeforeSetPriorityOfObjectStateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setPriorityOfObjectState($objectState, $priority);

        $this->eventDispatcher->dispatch(
            new SetPriorityOfObjectStateEvent(...$eventData)
        );
    }

    public function deleteObjectState(ObjectState $objectState): void
    {
        $eventData = [$objectState];

        $beforeEvent = new BeforeDeleteObjectStateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteObjectState($objectState);

        $this->eventDispatcher->dispatch(
            new DeleteObjectStateEvent(...$eventData)
        );
    }

    public function setContentState(
        ContentInfo $contentInfo,
        ObjectStateGroup $objectStateGroup,
        ObjectState $objectState
    ): void {
        $eventData = [
            $contentInfo,
            $objectStateGroup,
            $objectState,
        ];

        $beforeEvent = new BeforeSetContentStateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setContentState($contentInfo, $objectStateGroup, $objectState);

        $this->eventDispatcher->dispatch(
            new SetContentStateEvent(...$eventData)
        );
    }
}

class_alias(ObjectStateService::class, 'eZ\Publish\Core\Event\ObjectStateService');
