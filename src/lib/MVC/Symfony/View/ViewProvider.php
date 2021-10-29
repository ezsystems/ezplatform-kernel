<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

interface ViewProvider
{
    /**
     * @return View
     */
    public function getView(View $view);
}

class_alias(ViewProvider::class, 'eZ\Publish\Core\MVC\Symfony\View\ViewProvider');
