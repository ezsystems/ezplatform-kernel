<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Notification\Renderer;

class Registry
{
    /** @var \Ibexa\Core\Notification\Renderer\NotificationRenderer[] */
    protected $registry = [];

    /**
     * @param string $alias
     * @param \Ibexa\Core\Notification\Renderer\NotificationRenderer $notificationRenderer
     */
    public function addRenderer(string $alias, NotificationRenderer $notificationRenderer): void
    {
        $this->registry[$alias] = $notificationRenderer;
    }

    /**
     * @param string $alias
     *
     * @return \Ibexa\Core\Notification\Renderer\NotificationRenderer
     */
    public function getRenderer(string $alias): NotificationRenderer
    {
        return $this->registry[$alias];
    }

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function hasRenderer(string $alias): bool
    {
        return isset($this->registry[$alias]);
    }
}

class_alias(Registry::class, 'eZ\Publish\Core\Notification\Renderer\Registry');
