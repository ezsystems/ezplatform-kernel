<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

/**
 * A view that can be cached over HTTP.
 *
 * Should allow
 */
interface CachableView
{
    /**
     * Sets the cache as enabled/disabled.
     *
     * @param bool $cacheEnabled
     */
    public function setCacheEnabled($cacheEnabled);

    /**
     * Indicates if cache is enabled or not.
     *
     * @return bool
     */
    public function isCacheEnabled();
}

class_alias(CachableView::class, 'eZ\Publish\Core\MVC\Symfony\View\CachableView');
