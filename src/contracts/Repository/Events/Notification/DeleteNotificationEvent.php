<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Notification;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;

final class DeleteNotificationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Notification\Notification */
    private $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}

class_alias(DeleteNotificationEvent::class, 'eZ\Publish\API\Repository\Events\Notification\DeleteNotificationEvent');
