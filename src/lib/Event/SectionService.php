<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\SectionServiceDecorator;
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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SectionService extends SectionServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        SectionServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createSection(SectionCreateStruct $sectionCreateStruct): Section
    {
        $eventData = [$sectionCreateStruct];

        $beforeEvent = new BeforeCreateSectionEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSection();
        }

        $section = $beforeEvent->hasSection()
            ? $beforeEvent->getSection()
            : $this->innerService->createSection($sectionCreateStruct);

        $this->eventDispatcher->dispatch(
            new CreateSectionEvent($section, ...$eventData)
        );

        return $section;
    }

    public function updateSection(
        Section $section,
        SectionUpdateStruct $sectionUpdateStruct
    ): Section {
        $eventData = [
            $section,
            $sectionUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateSectionEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedSection();
        }

        $updatedSection = $beforeEvent->hasUpdatedSection()
            ? $beforeEvent->getUpdatedSection()
            : $this->innerService->updateSection($section, $sectionUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateSectionEvent($updatedSection, ...$eventData)
        );

        return $updatedSection;
    }

    public function assignSection(
        ContentInfo $contentInfo,
        Section $section
    ): void {
        $eventData = [
            $contentInfo,
            $section,
        ];

        $beforeEvent = new BeforeAssignSectionEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignSection($contentInfo, $section);

        $this->eventDispatcher->dispatch(
            new AssignSectionEvent(...$eventData)
        );
    }

    public function assignSectionToSubtree(
        Location $location,
        Section $section
    ): void {
        $eventData = [
            $location,
            $section,
        ];

        $beforeEvent = new BeforeAssignSectionToSubtreeEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignSectionToSubtree($location, $section);

        $this->eventDispatcher->dispatch(
            new AssignSectionToSubtreeEvent(...$eventData)
        );
    }

    public function deleteSection(Section $section): void
    {
        $eventData = [$section];

        $beforeEvent = new BeforeDeleteSectionEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteSection($section);

        $this->eventDispatcher->dispatch(
            new DeleteSectionEvent(...$eventData)
        );
    }
}

class_alias(SectionService::class, 'eZ\Publish\Core\Event\SectionService');
