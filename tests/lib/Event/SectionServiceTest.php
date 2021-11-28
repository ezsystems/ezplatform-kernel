<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Event;

use Ibexa\Contracts\Core\Repository\Events\Section\AssignSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\AssignSectionToSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\BeforeAssignSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\BeforeAssignSectionToSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\BeforeCreateSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\BeforeDeleteSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\BeforeUpdateSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\CreateSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\DeleteSectionEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\UpdateSectionEvent;
use Ibexa\Contracts\Core\Repository\SectionService as SectionServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct;
use Ibexa\Core\Event\SectionService;

class SectionServiceTest extends AbstractServiceTest
{
    public function testAssignSectionEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignSectionEvent::class,
            AssignSectionEvent::class
        );

        $parameters = [
            $this->createMock(ContentInfo::class),
            $this->createMock(Section::class),
        ];

        $innerServiceMock = $this->createMock(SectionServiceInterface::class);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $service->assignSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignSectionEvent::class, 0],
            [AssignSectionEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testAssignSectionStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignSectionEvent::class,
            AssignSectionEvent::class
        );

        $parameters = [
            $this->createMock(ContentInfo::class),
            $this->createMock(Section::class),
        ];

        $innerServiceMock = $this->createMock(SectionServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeAssignSectionEvent::class, static function (BeforeAssignSectionEvent $event) {
            $event->stopPropagation();
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $service->assignSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignSectionEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [AssignSectionEvent::class, 0],
            [BeforeAssignSectionEvent::class, 0],
        ]);
    }

    public function testUpdateSectionEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSectionEvent::class,
            UpdateSectionEvent::class
        );

        $parameters = [
            $this->createMock(Section::class),
            $this->createMock(SectionUpdateStruct::class),
        ];

        $updatedSection = $this->createMock(Section::class);
        $innerServiceMock = $this->createMock(SectionServiceInterface::class);
        $innerServiceMock->method('updateSection')->willReturn($updatedSection);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedSection, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateSectionEvent::class, 0],
            [UpdateSectionEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdateSectionResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSectionEvent::class,
            UpdateSectionEvent::class
        );

        $parameters = [
            $this->createMock(Section::class),
            $this->createMock(SectionUpdateStruct::class),
        ];

        $updatedSection = $this->createMock(Section::class);
        $eventUpdatedSection = $this->createMock(Section::class);
        $innerServiceMock = $this->createMock(SectionServiceInterface::class);
        $innerServiceMock->method('updateSection')->willReturn($updatedSection);

        $traceableEventDispatcher->addListener(BeforeUpdateSectionEvent::class, static function (BeforeUpdateSectionEvent $event) use ($eventUpdatedSection) {
            $event->setUpdatedSection($eventUpdatedSection);
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedSection, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateSectionEvent::class, 10],
            [BeforeUpdateSectionEvent::class, 0],
            [UpdateSectionEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateSectionStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateSectionEvent::class,
            UpdateSectionEvent::class
        );

        $parameters = [
            $this->createMock(Section::class),
            $this->createMock(SectionUpdateStruct::class),
        ];

        $updatedSection = $this->createMock(Section::class);
        $eventUpdatedSection = $this->createMock(Section::class);
        $innerServiceMock = $this->createMock(SectionServiceInterface::class);
        $innerServiceMock->method('updateSection')->willReturn($updatedSection);

        $traceableEventDispatcher->addListener(BeforeUpdateSectionEvent::class, static function (BeforeUpdateSectionEvent $event) use ($eventUpdatedSection) {
            $event->setUpdatedSection($eventUpdatedSection);
            $event->stopPropagation();
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedSection, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateSectionEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUpdateSectionEvent::class, 0],
            [UpdateSectionEvent::class, 0],
        ]);
    }

    public function testAssignSectionToSubtreeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignSectionToSubtreeEvent::class,
            AssignSectionToSubtreeEvent::class
        );

        $parameters = [
            $this->createMock(Location::class),
            $this->createMock(Section::class),
        ];

        $innerServiceMock = $this->createMock(SectionServiceInterface::class);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $service->assignSectionToSubtree(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignSectionToSubtreeEvent::class, 0],
            [AssignSectionToSubtreeEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testAssignSectionToSubtreeStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignSectionToSubtreeEvent::class,
            AssignSectionToSubtreeEvent::class
        );

        $parameters = [
            $this->createMock(Location::class),
            $this->createMock(Section::class),
        ];

        $innerServiceMock = $this->createMock(SectionServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeAssignSectionToSubtreeEvent::class, static function (BeforeAssignSectionToSubtreeEvent $event) {
            $event->stopPropagation();
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $service->assignSectionToSubtree(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignSectionToSubtreeEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [AssignSectionToSubtreeEvent::class, 0],
            [BeforeAssignSectionToSubtreeEvent::class, 0],
        ]);
    }

    public function testDeleteSectionEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteSectionEvent::class,
            DeleteSectionEvent::class
        );

        $parameters = [
            $this->createMock(Section::class),
        ];

        $innerServiceMock = $this->createMock(SectionServiceInterface::class);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeleteSectionEvent::class, 0],
            [DeleteSectionEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testDeleteSectionStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteSectionEvent::class,
            DeleteSectionEvent::class
        );

        $parameters = [
            $this->createMock(Section::class),
        ];

        $innerServiceMock = $this->createMock(SectionServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeDeleteSectionEvent::class, static function (BeforeDeleteSectionEvent $event) {
            $event->stopPropagation();
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeleteSectionEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeDeleteSectionEvent::class, 0],
            [DeleteSectionEvent::class, 0],
        ]);
    }

    public function testCreateSectionEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSectionEvent::class,
            CreateSectionEvent::class
        );

        $parameters = [
            $this->createMock(SectionCreateStruct::class),
        ];

        $section = $this->createMock(Section::class);
        $innerServiceMock = $this->createMock(SectionServiceInterface::class);
        $innerServiceMock->method('createSection')->willReturn($section);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($section, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateSectionEvent::class, 0],
            [CreateSectionEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnCreateSectionResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSectionEvent::class,
            CreateSectionEvent::class
        );

        $parameters = [
            $this->createMock(SectionCreateStruct::class),
        ];

        $section = $this->createMock(Section::class);
        $eventSection = $this->createMock(Section::class);
        $innerServiceMock = $this->createMock(SectionServiceInterface::class);
        $innerServiceMock->method('createSection')->willReturn($section);

        $traceableEventDispatcher->addListener(BeforeCreateSectionEvent::class, static function (BeforeCreateSectionEvent $event) use ($eventSection) {
            $event->setSection($eventSection);
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventSection, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateSectionEvent::class, 10],
            [BeforeCreateSectionEvent::class, 0],
            [CreateSectionEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testCreateSectionStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateSectionEvent::class,
            CreateSectionEvent::class
        );

        $parameters = [
            $this->createMock(SectionCreateStruct::class),
        ];

        $section = $this->createMock(Section::class);
        $eventSection = $this->createMock(Section::class);
        $innerServiceMock = $this->createMock(SectionServiceInterface::class);
        $innerServiceMock->method('createSection')->willReturn($section);

        $traceableEventDispatcher->addListener(BeforeCreateSectionEvent::class, static function (BeforeCreateSectionEvent $event) use ($eventSection) {
            $event->setSection($eventSection);
            $event->stopPropagation();
        }, 10);

        $service = new SectionService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createSection(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventSection, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateSectionEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeCreateSectionEvent::class, 0],
            [CreateSectionEvent::class, 0],
        ]);
    }
}

class_alias(SectionServiceTest::class, 'eZ\Publish\Core\Event\Tests\SectionServiceTest');
