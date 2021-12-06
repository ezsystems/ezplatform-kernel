<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct;

abstract class ObjectStateServiceDecorator implements ObjectStateService
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    protected $innerService;

    public function __construct(ObjectStateService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createObjectStateGroup(ObjectStateGroupCreateStruct $objectStateGroupCreateStruct): ObjectStateGroup
    {
        return $this->innerService->createObjectStateGroup($objectStateGroupCreateStruct);
    }

    public function loadObjectStateGroup(
        int $objectStateGroupId,
        array $prioritizedLanguages = []
    ): ObjectStateGroup {
        return $this->innerService->loadObjectStateGroup($objectStateGroupId, $prioritizedLanguages);
    }

    public function loadObjectStateGroupByIdentifier(
        string $objectStateGroupIdentifier,
        array $prioritizedLanguages = []
    ): ObjectStateGroup {
        return $this->innerService->loadObjectStateGroupByIdentifier($objectStateGroupIdentifier, $prioritizedLanguages);
    }

    public function loadObjectStateGroups(
        int $offset = 0,
        int $limit = -1,
        array $prioritizedLanguages = []
    ): iterable {
        return $this->innerService->loadObjectStateGroups($offset, $limit, $prioritizedLanguages);
    }

    public function loadObjectStates(
        ObjectStateGroup $objectStateGroup,
        array $prioritizedLanguages = []
    ): iterable {
        return $this->innerService->loadObjectStates($objectStateGroup, $prioritizedLanguages);
    }

    public function updateObjectStateGroup(
        ObjectStateGroup $objectStateGroup,
        ObjectStateGroupUpdateStruct $objectStateGroupUpdateStruct
    ): ObjectStateGroup {
        return $this->innerService->updateObjectStateGroup($objectStateGroup, $objectStateGroupUpdateStruct);
    }

    public function deleteObjectStateGroup(ObjectStateGroup $objectStateGroup): void
    {
        $this->innerService->deleteObjectStateGroup($objectStateGroup);
    }

    public function createObjectState(
        ObjectStateGroup $objectStateGroup,
        ObjectStateCreateStruct $objectStateCreateStruct
    ): ObjectState {
        return $this->innerService->createObjectState($objectStateGroup, $objectStateCreateStruct);
    }

    public function loadObjectState(
        int $stateId,
        array $prioritizedLanguages = []
    ): ObjectState {
        return $this->innerService->loadObjectState($stateId, $prioritizedLanguages);
    }

    public function loadObjectStateByIdentifier(
        ObjectStateGroup $objectStateGroup,
        string $objectStateIdentifier,
        array $prioritizedLanguages = []
    ): ObjectState {
        return $this->innerService->loadObjectStateByIdentifier(
            $objectStateGroup,
            $objectStateIdentifier,
            $prioritizedLanguages
        );
    }

    public function updateObjectState(
        ObjectState $objectState,
        ObjectStateUpdateStruct $objectStateUpdateStruct
    ): ObjectState {
        return $this->innerService->updateObjectState($objectState, $objectStateUpdateStruct);
    }

    public function setPriorityOfObjectState(
        ObjectState $objectState,
        int $priority
    ): void {
        $this->innerService->setPriorityOfObjectState($objectState, $priority);
    }

    public function deleteObjectState(ObjectState $objectState): void
    {
        $this->innerService->deleteObjectState($objectState);
    }

    public function setContentState(
        ContentInfo $contentInfo,
        ObjectStateGroup $objectStateGroup,
        ObjectState $objectState
    ): void {
        $this->innerService->setContentState($contentInfo, $objectStateGroup, $objectState);
    }

    public function getContentState(
        ContentInfo $contentInfo,
        ObjectStateGroup $objectStateGroup
    ): ObjectState {
        return $this->innerService->getContentState($contentInfo, $objectStateGroup);
    }

    public function getContentCount(ObjectState $objectState): int
    {
        return $this->innerService->getContentCount($objectState);
    }

    public function newObjectStateGroupCreateStruct(string $identifier): ObjectStateGroupCreateStruct
    {
        return $this->innerService->newObjectStateGroupCreateStruct($identifier);
    }

    public function newObjectStateGroupUpdateStruct(): ObjectStateGroupUpdateStruct
    {
        return $this->innerService->newObjectStateGroupUpdateStruct();
    }

    public function newObjectStateCreateStruct(string $identifier): ObjectStateCreateStruct
    {
        return $this->innerService->newObjectStateCreateStruct($identifier);
    }

    public function newObjectStateUpdateStruct(): ObjectStateUpdateStruct
    {
        return $this->innerService->newObjectStateUpdateStruct();
    }
}

class_alias(ObjectStateServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\ObjectStateServiceDecorator');
