<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\URLWildcardServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\BeforeCreateEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\BeforeRemoveEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\BeforeTranslateEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\BeforeUpdateEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\CreateEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\RemoveEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\TranslateEvent;
use Ibexa\Contracts\Core\Repository\Events\URLWildcard\UpdateEvent;
use Ibexa\Contracts\Core\Repository\URLWildcardService as URLWildcardServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcardTranslationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcardUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class URLWildcardService extends URLWildcardServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        URLWildcardServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(
        string $sourceUrl,
        string $destinationUrl,
        bool $forward = false
    ): UrlWildcard {
        $eventData = [
            $sourceUrl,
            $destinationUrl,
            $forward,
        ];

        $beforeEvent = new BeforeCreateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUrlWildcard();
        }

        $urlWildcard = $beforeEvent->hasUrlWildcard()
            ? $beforeEvent->getUrlWildcard()
            : $this->innerService->create($sourceUrl, $destinationUrl, $forward);

        $this->eventDispatcher->dispatch(
            new CreateEvent($urlWildcard, ...$eventData)
        );

        return $urlWildcard;
    }

    public function update(
        URLWildcard $urlWildcard,
        URLWildcardUpdateStruct $updateStruct
    ): void {
        $eventData = [
            $urlWildcard,
            $updateStruct,
        ];

        $beforeEvent = new BeforeUpdateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->update($urlWildcard, $updateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateEvent(...$eventData)
        );
    }

    public function remove(URLWildcard $urlWildcard): void
    {
        $eventData = [$urlWildcard];

        $beforeEvent = new BeforeRemoveEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->remove($urlWildcard);

        $this->eventDispatcher->dispatch(
            new RemoveEvent(...$eventData)
        );
    }

    public function translate(string $url): URLWildcardTranslationResult
    {
        $eventData = [$url];

        $beforeEvent = new BeforeTranslateEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResult();
        }

        $result = $beforeEvent->hasResult()
            ? $beforeEvent->getResult()
            : $this->innerService->translate($url);

        $this->eventDispatcher->dispatch(
            new TranslateEvent($result, ...$eventData)
        );

        return $result;
    }
}

class_alias(URLWildcardService::class, 'eZ\Publish\Core\Event\URLWildcardService');
