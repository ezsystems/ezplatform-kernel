<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Repository\Tests\Decorator;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateCreateStruct;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroupCreateStruct;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateUpdateStruct;
use eZ\Publish\SPI\Repository\Decorator\ObjectStateServiceDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ObjectStateServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): ObjectStateService
    {
        return new class($service) extends ObjectStateServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(ObjectStateService::class);
    }

    public function testCreateObjectStateGroupDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(ObjectStateGroupCreateStruct::class)];

        $serviceMock->expects($this->once())->method('createObjectStateGroup')->with(...$parameters);

        $decoratedService->createObjectStateGroup(...$parameters);
    }

    public function testLoadObjectStateGroupDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            5518074,
            ['eng-GB'],
        ];

        $serviceMock->expects($this->once())->method('loadObjectStateGroup')->with(...$parameters);

        $decoratedService->loadObjectStateGroup(...$parameters);
    }

    public function testLoadObjectStateGroupByIdentifierDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);
        $expectedObjectStateGroup = $this->createMock(ObjectStateGroup::class);

        $parameters = [
            'ez_lock',
            ['eng-GB'],
        ];

        $serviceMock
            ->expects($this->once())
            ->method('loadObjectStateGroupByIdentifier')
            ->with(...$parameters)
            ->willReturn($expectedObjectStateGroup);

        $actualObjectStateGroup = $decoratedService->loadObjectStateGroupByIdentifier(...$parameters);

        $this->assertEquals(
            $expectedObjectStateGroup,
            $actualObjectStateGroup
        );
    }

    public function testLoadObjectStateGroupsDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            50,
            100,
            ['eng-GB'],
        ];

        $serviceMock->expects($this->once())->method('loadObjectStateGroups')->with(...$parameters);

        $decoratedService->loadObjectStateGroups(...$parameters);
    }

    public function testLoadObjectStatesDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ObjectStateGroup::class),
            ['random_value_5ced05ce168263.48122762'],
        ];

        $serviceMock->expects($this->once())->method('loadObjectStates')->with(...$parameters);

        $decoratedService->loadObjectStates(...$parameters);
    }

    public function testUpdateObjectStateGroupDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ObjectStateGroup::class),
            $this->createMock(ObjectStateGroupUpdateStruct::class),
        ];

        $serviceMock->expects($this->once())->method('updateObjectStateGroup')->with(...$parameters);

        $decoratedService->updateObjectStateGroup(...$parameters);
    }

    public function testDeleteObjectStateGroupDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(ObjectStateGroup::class)];

        $serviceMock->expects($this->once())->method('deleteObjectStateGroup')->with(...$parameters);

        $decoratedService->deleteObjectStateGroup(...$parameters);
    }

    public function testCreateObjectStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ObjectStateGroup::class),
            $this->createMock(ObjectStateCreateStruct::class),
        ];

        $serviceMock->expects($this->once())->method('createObjectState')->with(...$parameters);

        $decoratedService->createObjectState(...$parameters);
    }

    public function testLoadObjectStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            95274945,
            ['eng-GB'],
        ];

        $serviceMock->expects($this->once())->method('loadObjectState')->with(...$parameters);

        $decoratedService->loadObjectState(...$parameters);
    }

    public function testLoadObjectStateDecoratorByIdentifier(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);
        $expectedObjectState = $this->createMock(ObjectState::class);

        $parameters = [
            $this->createMock(ObjectStateGroup::class),
            'locked',
            ['eng-GB'],
        ];

        $serviceMock
            ->expects($this->once())
            ->method('loadObjectStateByIdentifier')
            ->with(...$parameters)
            ->willReturn($expectedObjectState);

        $actualObjectState = $decoratedService->loadObjectStateByIdentifier(...$parameters);

        $this->assertEquals(
            $expectedObjectState,
            $actualObjectState
        );
    }

    public function testUpdateObjectStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ObjectState::class),
            $this->createMock(ObjectStateUpdateStruct::class),
        ];

        $serviceMock->expects($this->once())->method('updateObjectState')->with(...$parameters);

        $decoratedService->updateObjectState(...$parameters);
    }

    public function testSetPriorityOfObjectStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ObjectState::class),
            100,
        ];

        $serviceMock->expects($this->once())->method('setPriorityOfObjectState')->with(...$parameters);

        $decoratedService->setPriorityOfObjectState(...$parameters);
    }

    public function testDeleteObjectStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(ObjectState::class)];

        $serviceMock->expects($this->once())->method('deleteObjectState')->with(...$parameters);

        $decoratedService->deleteObjectState(...$parameters);
    }

    public function testSetContentStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ContentInfo::class),
            $this->createMock(ObjectStateGroup::class),
            $this->createMock(ObjectState::class),
        ];

        $serviceMock->expects($this->once())->method('setContentState')->with(...$parameters);

        $decoratedService->setContentState(...$parameters);
    }

    public function testGetContentStateDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(ContentInfo::class),
            $this->createMock(ObjectStateGroup::class),
        ];

        $serviceMock->expects($this->once())->method('getContentState')->with(...$parameters);

        $decoratedService->getContentState(...$parameters);
    }

    public function testGetContentCountDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(ObjectState::class)];

        $serviceMock->expects($this->once())->method('getContentCount')->with(...$parameters);

        $decoratedService->getContentCount(...$parameters);
    }

    public function testNewObjectStateGroupCreateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce169c83.55416136'];

        $serviceMock->expects($this->once())->method('newObjectStateGroupCreateStruct')->with(...$parameters);

        $decoratedService->newObjectStateGroupCreateStruct(...$parameters);
    }

    public function testNewObjectStateGroupUpdateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('newObjectStateGroupUpdateStruct')->with(...$parameters);

        $decoratedService->newObjectStateGroupUpdateStruct(...$parameters);
    }

    public function testNewObjectStateCreateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce169cc9.01447563'];

        $serviceMock->expects($this->once())->method('newObjectStateCreateStruct')->with(...$parameters);

        $decoratedService->newObjectStateCreateStruct(...$parameters);
    }

    public function testNewObjectStateUpdateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('newObjectStateUpdateStruct')->with(...$parameters);

        $decoratedService->newObjectStateUpdateStruct(...$parameters);
    }
}
