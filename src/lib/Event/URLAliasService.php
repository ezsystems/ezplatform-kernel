<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\URLAliasServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\BeforeCreateGlobalUrlAliasEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\BeforeCreateUrlAliasEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\BeforeRefreshSystemUrlAliasesForLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\BeforeRemoveAliasesEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\CreateGlobalUrlAliasEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\CreateUrlAliasEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\RefreshSystemUrlAliasesForLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\URLAlias\RemoveAliasesEvent;
use Ibexa\Contracts\Core\Repository\URLAliasService as URLAliasServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class URLAliasService extends URLAliasServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        URLAliasServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createUrlAlias(
        Location $location,
        string $path,
        string $languageCode,
        bool $forwarding = false,
        bool $alwaysAvailable = false
    ): URLAlias {
        $eventData = [
            $location,
            $path,
            $languageCode,
            $forwarding,
            $alwaysAvailable,
        ];

        $beforeEvent = new BeforeCreateUrlAliasEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUrlAlias();
        }

        $urlAlias = $beforeEvent->hasUrlAlias()
            ? $beforeEvent->getUrlAlias()
            : $this->innerService->createUrlAlias($location, $path, $languageCode, $forwarding, $alwaysAvailable);

        $this->eventDispatcher->dispatch(
            new CreateUrlAliasEvent($urlAlias, ...$eventData)
        );

        return $urlAlias;
    }

    public function createGlobalUrlAlias(
        string $resource,
        string $path,
        string $languageCode,
        bool $forwarding = false,
        bool $alwaysAvailable = false
    ): URLAlias {
        $eventData = [
            $resource,
            $path,
            $languageCode,
            $forwarding,
            $alwaysAvailable,
        ];

        $beforeEvent = new BeforeCreateGlobalUrlAliasEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUrlAlias();
        }

        $urlAlias = $beforeEvent->hasUrlAlias()
            ? $beforeEvent->getUrlAlias()
            : $this->innerService->createGlobalUrlAlias($resource, $path, $languageCode, $forwarding, $alwaysAvailable);

        $this->eventDispatcher->dispatch(
            new CreateGlobalUrlAliasEvent($urlAlias, ...$eventData)
        );

        return $urlAlias;
    }

    public function removeAliases(array $aliasList): void
    {
        $eventData = [$aliasList];

        $beforeEvent = new BeforeRemoveAliasesEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->removeAliases($aliasList);

        $this->eventDispatcher->dispatch(
            new RemoveAliasesEvent(...$eventData)
        );
    }

    public function refreshSystemUrlAliasesForLocation(Location $location): void
    {
        $eventData = [$location];

        $beforeEvent = new BeforeRefreshSystemUrlAliasesForLocationEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->refreshSystemUrlAliasesForLocation($location);

        $this->eventDispatcher->dispatch(
            new RefreshSystemUrlAliasesForLocationEvent(...$eventData)
        );
    }
}

class_alias(URLAliasService::class, 'eZ\Publish\Core\Event\URLAliasService');
