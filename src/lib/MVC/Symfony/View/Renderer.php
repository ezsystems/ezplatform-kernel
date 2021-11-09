<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

/**
 * Renders a View to a string representation.
 */
interface Renderer
{
    /**
     * @param \Ibexa\Core\MVC\Symfony\View\View $view
     *
     * @return string
     */
    public function render(View $view);
}

class_alias(Renderer::class, 'eZ\Publish\Core\MVC\Symfony\View\Renderer');
