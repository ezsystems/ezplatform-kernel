<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\NotificationServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Notification\BeforeCreateNotificationEvent;
use Ibexa\Contracts\Core\Repository\Events\Notification\BeforeDeleteNotificationEvent;
use Ibexa\Contracts\Core\Repository\Events\Notification\BeforeMarkNotificationAsReadEvent;
use Ibexa\Contracts\Core\Repository\Events\Notification\CreateNotificationEvent;
use Ibexa\Contracts\Core\Repository\Events\Notification\DeleteNotificationEvent;
use Ibexa\Contracts\Core\Repository\Events\Notification\MarkNotificationAsReadEvent;
use Ibexa\Contracts\Core\Repository\NotificationService as NotificationServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NotificationService extends NotificationServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        NotificationServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function markNotificationAsRead(Notification $notification): void
    {
        $eventData = [$notification];

        $beforeEvent = new BeforeMarkNotificationAsReadEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->markNotificationAsRead($notification);

        $this->eventDispatcher->dispatch(
            new MarkNotificationAsReadEvent(...$eventData)
        );
    }

    public function createNotification(CreateStruct $createStruct): Notification
    {
        $eventData = [$createStruct];

        $beforeEvent = new BeforeCreateNotificationEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getNotification();
        }

        $notification = $beforeEvent->hasNotification()
            ? $beforeEvent->getNotification()
            : $this->innerService->createNotification($createStruct);

        $this->eventDispatcher->dispatch(
            new CreateNotificationEvent($notification, ...$eventData)
        );

        return $notification;
    }

    public function deleteNotification(Notification $notification): void
    {
        $eventData = [$notification];

        $beforeEvent = new BeforeDeleteNotificationEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteNotification($notification);

        $this->eventDispatcher->dispatch(
            new DeleteNotificationEvent(...$eventData)
        );
    }
}

class_alias(NotificationService::class, 'eZ\Publish\Core\Event\NotificationService');
