<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Notification\Renderer;

use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;

interface NotificationRenderer
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     *
     * @return string
     */
    public function render(Notification $notification): string;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     *
     * @return string|null
     */
    public function generateUrl(Notification $notification): ?string;
}

class_alias(NotificationRenderer::class, 'eZ\Publish\Core\Notification\Renderer\NotificationRenderer');
