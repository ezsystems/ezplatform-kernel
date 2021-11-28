<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Notification;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use UnexpectedValueException;

final class BeforeCreateNotificationEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct */
    private $createStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Notification\Notification|null */
    private $notification;

    public function __construct(CreateStruct $createStruct)
    {
        $this->createStruct = $createStruct;
    }

    public function getCreateStruct(): CreateStruct
    {
        return $this->createStruct;
    }

    public function getNotification(): Notification
    {
        if (!$this->hasNotification()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasNotification() or set it using setNotification() before you call the getter.', Notification::class));
        }

        return $this->notification;
    }

    public function setNotification(?Notification $notification): void
    {
        $this->notification = $notification;
    }

    public function hasNotification(): bool
    {
        return $this->notification instanceof Notification;
    }
}

class_alias(BeforeCreateNotificationEvent::class, 'eZ\Publish\API\Repository\Events\Notification\BeforeCreateNotificationEvent');
