<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\URLServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\URL\BeforeUpdateUrlEvent;
use Ibexa\Contracts\Core\Repository\Events\URL\UpdateUrlEvent;
use Ibexa\Contracts\Core\Repository\URLService as URLServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class URLService extends URLServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        URLServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function updateUrl(
        URL $url,
        URLUpdateStruct $struct
    ): URL {
        $eventData = [
            $url,
            $struct,
        ];

        $beforeEvent = new BeforeUpdateUrlEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedUrl();
        }

        $updatedUrl = $beforeEvent->hasUpdatedUrl()
            ? $beforeEvent->getUpdatedUrl()
            : $this->innerService->updateUrl($url, $struct);

        $this->eventDispatcher->dispatch(
            new UpdateUrlEvent($updatedUrl, ...$eventData)
        );

        return $updatedUrl;
    }
}

class_alias(URLService::class, 'eZ\Publish\Core\Event\URLService');
