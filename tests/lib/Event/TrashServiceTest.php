<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Event;

use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeDeleteTrashItemEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeEmptyTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeRecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\DeleteTrashItemEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\EmptyTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\TrashService as TrashServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use Ibexa\Core\Event\TrashService;

class TrashServiceTest extends AbstractServiceTest
{
    public function testEmptyTrashEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeEmptyTrashEvent::class,
            EmptyTrashEvent::class
        );

        $parameters = [
        ];

        $resultList = $this->createMock(TrashItemDeleteResultList::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('emptyTrash')->willReturn($resultList);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->emptyTrash(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($resultList, $result);
        $this->assertSame($calledListeners, [
            [BeforeEmptyTrashEvent::class, 0],
            [EmptyTrashEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnEmptyTrashResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeEmptyTrashEvent::class,
            EmptyTrashEvent::class
        );

        $parameters = [
        ];

        $resultList = $this->createMock(TrashItemDeleteResultList::class);
        $eventResultList = $this->createMock(TrashItemDeleteResultList::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('emptyTrash')->willReturn($resultList);

        $traceableEventDispatcher->addListener(BeforeEmptyTrashEvent::class, static function (BeforeEmptyTrashEvent $event) use ($eventResultList) {
            $event->setResultList($eventResultList);
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->emptyTrash(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventResultList, $result);
        $this->assertSame($calledListeners, [
            [BeforeEmptyTrashEvent::class, 10],
            [BeforeEmptyTrashEvent::class, 0],
            [EmptyTrashEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testEmptyTrashStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeEmptyTrashEvent::class,
            EmptyTrashEvent::class
        );

        $parameters = [
        ];

        $resultList = $this->createMock(TrashItemDeleteResultList::class);
        $eventResultList = $this->createMock(TrashItemDeleteResultList::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('emptyTrash')->willReturn($resultList);

        $traceableEventDispatcher->addListener(BeforeEmptyTrashEvent::class, static function (BeforeEmptyTrashEvent $event) use ($eventResultList) {
            $event->setResultList($eventResultList);
            $event->stopPropagation();
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->emptyTrash(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventResultList, $result);
        $this->assertSame($calledListeners, [
            [BeforeEmptyTrashEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeEmptyTrashEvent::class, 0],
            [EmptyTrashEvent::class, 0],
        ]);
    }

    public function testTrashEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeTrashEvent::class,
            TrashEvent::class
        );

        $parameters = [
            $this->createMock(Location::class),
        ];

        $trashItem = $this->createMock(TrashItem::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('trash')->willReturn($trashItem);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->trash(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($trashItem, $result);
        $this->assertSame($calledListeners, [
            [BeforeTrashEvent::class, 0],
            [TrashEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnTrashResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeTrashEvent::class,
            TrashEvent::class
        );

        $parameters = [
            $this->createMock(Location::class),
        ];

        $trashItem = $this->createMock(TrashItem::class);
        $eventTrashItem = $this->createMock(TrashItem::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('trash')->willReturn($trashItem);

        $traceableEventDispatcher->addListener(BeforeTrashEvent::class, static function (BeforeTrashEvent $event) use ($eventTrashItem) {
            $event->setResult($eventTrashItem);
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->trash(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventTrashItem, $result);
        $this->assertSame($calledListeners, [
            [BeforeTrashEvent::class, 10],
            [BeforeTrashEvent::class, 0],
            [TrashEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testTrashStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeTrashEvent::class,
            TrashEvent::class
        );

        $parameters = [
            $this->createMock(Location::class),
        ];

        $trashItem = $this->createMock(TrashItem::class);
        $eventTrashItem = $this->createMock(TrashItem::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('trash')->willReturn($trashItem);

        $traceableEventDispatcher->addListener(BeforeTrashEvent::class, static function (BeforeTrashEvent $event) use ($eventTrashItem) {
            $event->setResult($eventTrashItem);
            $event->stopPropagation();
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->trash(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventTrashItem, $result);
        $this->assertSame($calledListeners, [
            [BeforeTrashEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeTrashEvent::class, 0],
            [TrashEvent::class, 0],
        ]);
    }

    public function testRecoverEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRecoverEvent::class,
            RecoverEvent::class
        );

        $parameters = [
            $this->createMock(TrashItem::class),
            $this->createMock(Location::class),
        ];

        $location = $this->createMock(Location::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('recover')->willReturn($location);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->recover(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($location, $result);
        $this->assertSame($calledListeners, [
            [BeforeRecoverEvent::class, 0],
            [RecoverEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnRecoverResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRecoverEvent::class,
            RecoverEvent::class
        );

        $parameters = [
            $this->createMock(TrashItem::class),
            $this->createMock(Location::class),
        ];

        $location = $this->createMock(Location::class);
        $eventLocation = $this->createMock(Location::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('recover')->willReturn($location);

        $traceableEventDispatcher->addListener(BeforeRecoverEvent::class, static function (BeforeRecoverEvent $event) use ($eventLocation) {
            $event->setLocation($eventLocation);
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->recover(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventLocation, $result);
        $this->assertSame($calledListeners, [
            [BeforeRecoverEvent::class, 10],
            [BeforeRecoverEvent::class, 0],
            [RecoverEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testRecoverStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRecoverEvent::class,
            RecoverEvent::class
        );

        $parameters = [
            $this->createMock(TrashItem::class),
            $this->createMock(Location::class),
        ];

        $location = $this->createMock(Location::class);
        $eventLocation = $this->createMock(Location::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('recover')->willReturn($location);

        $traceableEventDispatcher->addListener(BeforeRecoverEvent::class, static function (BeforeRecoverEvent $event) use ($eventLocation) {
            $event->setLocation($eventLocation);
            $event->stopPropagation();
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->recover(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventLocation, $result);
        $this->assertSame($calledListeners, [
            [BeforeRecoverEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeRecoverEvent::class, 0],
            [RecoverEvent::class, 0],
        ]);
    }

    public function testDeleteTrashItemEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteTrashItemEvent::class,
            DeleteTrashItemEvent::class
        );

        $parameters = [
            $this->createMock(TrashItem::class),
        ];

        $result = $this->createMock(TrashItemDeleteResult::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('deleteTrashItem')->willReturn($result);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->deleteTrashItem(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($result, $result);
        $this->assertSame($calledListeners, [
            [BeforeDeleteTrashItemEvent::class, 0],
            [DeleteTrashItemEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnDeleteTrashItemResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteTrashItemEvent::class,
            DeleteTrashItemEvent::class
        );

        $parameters = [
            $this->createMock(TrashItem::class),
        ];

        $result = $this->createMock(TrashItemDeleteResult::class);
        $eventResult = $this->createMock(TrashItemDeleteResult::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('deleteTrashItem')->willReturn($result);

        $traceableEventDispatcher->addListener(BeforeDeleteTrashItemEvent::class, static function (BeforeDeleteTrashItemEvent $event) use ($eventResult) {
            $event->setResult($eventResult);
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->deleteTrashItem(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventResult, $result);
        $this->assertSame($calledListeners, [
            [BeforeDeleteTrashItemEvent::class, 10],
            [BeforeDeleteTrashItemEvent::class, 0],
            [DeleteTrashItemEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testDeleteTrashItemStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteTrashItemEvent::class,
            DeleteTrashItemEvent::class
        );

        $parameters = [
            $this->createMock(TrashItem::class),
        ];

        $result = $this->createMock(TrashItemDeleteResult::class);
        $eventResult = $this->createMock(TrashItemDeleteResult::class);
        $innerServiceMock = $this->createMock(TrashServiceInterface::class);
        $innerServiceMock->method('deleteTrashItem')->willReturn($result);

        $traceableEventDispatcher->addListener(BeforeDeleteTrashItemEvent::class, static function (BeforeDeleteTrashItemEvent $event) use ($eventResult) {
            $event->setResult($eventResult);
            $event->stopPropagation();
        }, 10);

        $service = new TrashService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->deleteTrashItem(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventResult, $result);
        $this->assertSame($calledListeners, [
            [BeforeDeleteTrashItemEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeDeleteTrashItemEvent::class, 0],
            [DeleteTrashItemEvent::class, 0],
        ]);
    }
}

class_alias(TrashServiceTest::class, 'eZ\Publish\Core\Event\Tests\TrashServiceTest');
