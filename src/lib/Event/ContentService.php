<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\ContentService as ContentServiceInterface;
use Ibexa\Contracts\Core\Repository\Decorator\ContentServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Content\AddRelationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeAddRelationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeCopyContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeCreateContentDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeCreateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteRelationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeHideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforePublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeRevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeUpdateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeUpdateContentMetadataEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CopyContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CreateContentDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CreateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteRelationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\HideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\RevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentMetadataEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContentService extends ContentServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        ContentServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createContent(
        ContentCreateStruct $contentCreateStruct,
        array $locationCreateStructs = [],
        ?array $fieldIdentifiersToValidate = null
    ): Content {
        $eventData = [
            $contentCreateStruct,
            $locationCreateStructs,
            $fieldIdentifiersToValidate,
        ];

        $beforeEvent = new BeforeCreateContentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->createContent($contentCreateStruct, $locationCreateStructs, $fieldIdentifiersToValidate);

        $this->eventDispatcher->dispatch(
            new CreateContentEvent($content, ...$eventData)
        );

        return $content;
    }

    public function updateContentMetadata(
        ContentInfo $contentInfo,
        ContentMetadataUpdateStruct $contentMetadataUpdateStruct
    ): Content {
        $eventData = [
            $contentInfo,
            $contentMetadataUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateContentMetadataEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateContentMetadataEvent($content, ...$eventData)
        );

        return $content;
    }

    public function deleteContent(ContentInfo $contentInfo): iterable
    {
        $eventData = [$contentInfo];

        $beforeEvent = new BeforeDeleteContentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getLocations();
        }

        $locations = $beforeEvent->hasLocations()
            ? $beforeEvent->getLocations()
            : $this->innerService->deleteContent($contentInfo);

        $this->eventDispatcher->dispatch(
            new DeleteContentEvent($locations, ...$eventData)
        );

        return $locations;
    }

    public function createContentDraft(
        ContentInfo $contentInfo,
        ?VersionInfo $versionInfo = null,
        ?User $creator = null,
        ?Language $language = null
    ): Content {
        $eventData = [
            $contentInfo,
            $versionInfo,
            $creator,
            $language,
        ];

        $beforeEvent = new BeforeCreateContentDraftEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContentDraft();
        }

        $contentDraft = $beforeEvent->hasContentDraft()
            ? $beforeEvent->getContentDraft()
            : $this->innerService->createContentDraft($contentInfo, $versionInfo, $creator, $language);

        $this->eventDispatcher->dispatch(
            new CreateContentDraftEvent($contentDraft, ...$eventData)
        );

        return $contentDraft;
    }

    public function updateContent(
        VersionInfo $versionInfo,
        ContentUpdateStruct $contentUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Content {
        $eventData = [
            $versionInfo,
            $contentUpdateStruct,
            $fieldIdentifiersToValidate,
        ];

        $beforeEvent = new BeforeUpdateContentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->updateContent($versionInfo, $contentUpdateStruct, $fieldIdentifiersToValidate);

        $this->eventDispatcher->dispatch(
            new UpdateContentEvent($content, ...$eventData)
        );

        return $content;
    }

    public function publishVersion(VersionInfo $versionInfo, array $translations = Language::ALL): Content
    {
        $eventData = [
            $versionInfo,
            $translations,
        ];

        $beforeEvent = new BeforePublishVersionEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->publishVersion($versionInfo, $translations);

        $this->eventDispatcher->dispatch(
            new PublishVersionEvent($content, ...$eventData)
        );

        return $content;
    }

    public function deleteVersion(VersionInfo $versionInfo): void
    {
        $eventData = [$versionInfo];

        $beforeEvent = new BeforeDeleteVersionEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteVersion($versionInfo);

        $this->eventDispatcher->dispatch(
            new DeleteVersionEvent(...$eventData)
        );
    }

    public function copyContent(
        ContentInfo $contentInfo,
        LocationCreateStruct $destinationLocationCreateStruct,
        ?VersionInfo $versionInfo = null
    ): Content {
        $eventData = [
            $contentInfo,
            $destinationLocationCreateStruct,
            $versionInfo,
        ];

        $beforeEvent = new BeforeCopyContentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->copyContent($contentInfo, $destinationLocationCreateStruct, $versionInfo);

        $this->eventDispatcher->dispatch(
            new CopyContentEvent($content, ...$eventData)
        );

        return $content;
    }

    public function addRelation(
        VersionInfo $sourceVersion,
        ContentInfo $destinationContent
    ): Relation {
        $eventData = [
            $sourceVersion,
            $destinationContent,
        ];

        $beforeEvent = new BeforeAddRelationEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getRelation();
        }

        $relation = $beforeEvent->hasRelation()
            ? $beforeEvent->getRelation()
            : $this->innerService->addRelation($sourceVersion, $destinationContent);

        $this->eventDispatcher->dispatch(
            new AddRelationEvent($relation, ...$eventData)
        );

        return $relation;
    }

    public function deleteRelation(
        VersionInfo $sourceVersion,
        ContentInfo $destinationContent
    ): void {
        $eventData = [
            $sourceVersion,
            $destinationContent,
        ];

        $beforeEvent = new BeforeDeleteRelationEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteRelation($sourceVersion, $destinationContent);

        $this->eventDispatcher->dispatch(
            new DeleteRelationEvent(...$eventData)
        );
    }

    public function deleteTranslation(
        ContentInfo $contentInfo,
        string $languageCode
    ): void {
        $eventData = [
            $contentInfo,
            $languageCode,
        ];

        $beforeEvent = new BeforeDeleteTranslationEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteTranslation($contentInfo, $languageCode);

        $this->eventDispatcher->dispatch(
            new DeleteTranslationEvent(...$eventData)
        );
    }

    public function hideContent(ContentInfo $contentInfo): void
    {
        $eventData = [$contentInfo];

        $beforeEvent = new BeforeHideContentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->hideContent($contentInfo);

        $this->eventDispatcher->dispatch(
            new HideContentEvent(...$eventData)
        );
    }

    public function revealContent(ContentInfo $contentInfo): void
    {
        $eventData = [$contentInfo];

        $beforeEvent = new BeforeRevealContentEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->revealContent($contentInfo);

        $this->eventDispatcher->dispatch(
            new RevealContentEvent(...$eventData)
        );
    }
}

class_alias(ContentService::class, 'eZ\Publish\Core\Event\ContentService');
