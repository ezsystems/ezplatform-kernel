<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\Notification\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Notification\Handler as SPINotificationHandler;
use Ibexa\Contracts\Core\Persistence\Notification\Notification as SPINotification;
use Ibexa\Contracts\Core\Persistence\Notification\UpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;

/**
 * Test case for Persistence\Cache\NotificationHandler.
 */
class NotificationHandlerTest extends AbstractCacheHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function getHandlerMethodName(): string
    {
        return 'notificationHandler';
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlerClassName(): string
    {
        return SPINotificationHandler::class;
    }

    /**
     * {@inheritdoc}
     */
    public function providerForUnCachedMethods(): array
    {
        $ownerId = 7;
        $notificationId = 5;
        $notification = new Notification([
            'id' => $notificationId,
            'ownerId' => $ownerId,
        ]);

        // string $method, array $arguments, array? $tagGeneratingArguments, array? $keyGeneratingArguments, array? $tags, array? $key, ?mixed $returnValue
        return [
            [
                'createNotification',
                [
                    new CreateStruct(['ownerId' => $ownerId]),
                ],
                null,
                [
                    ['notification_count', [$ownerId], true],
                    ['notification_pending_count', [$ownerId], true],
                ],
                null,
                [
                    'ibx-nc-' . $ownerId,
                    'ibx-npc-' . $ownerId,
                ],
                new SPINotification(),
            ],
            [
                'updateNotification',
                [
                    $notification,
                    new UpdateStruct(['isPending' => false]),
                ],
                null,
                [
                    ['notification', [$notificationId], true],
                    ['notification_pending_count', [$ownerId], true],
                ],
                null,
                [
                    'ibx-n-' . $notificationId,
                    'ibx-npc-' . $ownerId,
                ],
                new SPINotification(),
            ],
            [
                'delete',
                [
                    $notification,
                ],
                null,
                [
                    ['notification', [$notificationId], true],
                    ['notification_count', [$ownerId], true],
                    ['notification_pending_count', [$ownerId], true],
                ],
                null,
                [
                    'ibx-n-' . $notificationId,
                    'ibx-nc-' . $ownerId,
                    'ibx-npc-' . $ownerId,
                ],
            ],
            [
                'loadUserNotifications', [$ownerId, 0, 25], null, null, null, null, [],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function providerForCachedLoadMethodsHit(): array
    {
        $notificationId = 5;
        $ownerId = 7;
        $notificationCount = 10;
        $notificationCountPending = 5;

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data, bool $multi
        return [
            [
                'countPendingNotifications',
                [
                    $ownerId,
                ],
                'ibx-npc-' . $ownerId,
                null,
                null,
                [['notification_pending_count', [$ownerId], true]],
                ['ibx-npc-' . $ownerId],
                $notificationCount,
            ],
            [
                'countNotifications',
                [
                    $ownerId,
                ],
                'ibx-nc-' . $ownerId,
                null,
                null,
                [['notification_count', [$ownerId], true]],
                ['ibx-nc-' . $ownerId],
                $notificationCountPending,
            ],
            [
                'getNotificationById',
                [
                    $notificationId,
                ],
                'ibx-n-' . $notificationId,
                null,
                null,
                [['notification', [$notificationId], true]],
                ['ibx-n-' . $notificationId],
                new SPINotification(['id' => $notificationId]),
            ],
        ];
    }

    public function providerForCachedLoadMethodsMiss(): array
    {
        $notificationId = 5;
        $ownerId = 7;
        $notificationCount = 10;
        $notificationCountPending = 5;

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data, bool $multi
        return [
            [
                'countPendingNotifications',
                [
                    $ownerId,
                ],
                'ibx-npc-' . $ownerId,
                null,
                null,
                [['notification_pending_count', [$ownerId], true]],
                ['ibx-npc-' . $ownerId],
                $notificationCount,
            ],
            [
                'countNotifications',
                [
                    $ownerId,
                ],
                'ibx-nc-' . $ownerId,
                null,
                null,
                [['notification_count', [$ownerId], true]],
                ['ibx-nc-' . $ownerId],
                $notificationCountPending,
            ],
            [
                'getNotificationById',
                [
                    $notificationId,
                ],
                'ibx-n-' . $notificationId,
                null,
                null,
                [['notification', [$notificationId], true]],
                ['ibx-n-' . $notificationId],
                new SPINotification(['id' => $notificationId]),
            ],
        ];
    }
}

class_alias(NotificationHandlerTest::class, 'eZ\Publish\Core\Persistence\Cache\Tests\NotificationHandlerTest');
