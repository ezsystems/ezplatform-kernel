<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\UserPreferenceServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\UserPreference\BeforeSetUserPreferenceEvent;
use Ibexa\Contracts\Core\Repository\Events\UserPreference\SetUserPreferenceEvent;
use Ibexa\Contracts\Core\Repository\UserPreferenceService as UserPreferenceServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserPreferenceService extends UserPreferenceServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        UserPreferenceServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function setUserPreference(array $userPreferenceSetStructs): void
    {
        $eventData = [$userPreferenceSetStructs];

        $beforeEvent = new BeforeSetUserPreferenceEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setUserPreference($userPreferenceSetStructs);

        $this->eventDispatcher->dispatch(
            new SetUserPreferenceEvent(...$eventData)
        );
    }
}

class_alias(UserPreferenceService::class, 'eZ\Publish\Core\Event\UserPreferenceService');
