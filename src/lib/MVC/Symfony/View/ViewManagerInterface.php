<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

interface ViewManagerInterface
{
    public const VIEW_TYPE_FULL = 'full';
    public const VIEW_TYPE_LINE = 'line';

    /**
     * Renders $content by selecting the right template.
     * $content will be injected in the selected template.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $viewType Variation of display for your content. Default is 'full'.
     * @param array $parameters Parameters to pass to the template called to
     *        render the view. By default, it's empty. 'content' entry is
     *        reserved for the Content that is rendered.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContent(Content $content, $viewType = self::VIEW_TYPE_FULL, $parameters = []);

    /**
     * Renders $location by selecting the right template for $viewType.
     * $content and $location will be injected in the selected template.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param string $viewType Variation of display for your content. Default is 'full'.
     * @param array $parameters Parameters to pass to the template called to
     *        render the view. By default, it's empty. 'location' and 'content'
     *        entries are reserved for the Location (and its Content) that is
     *        viewed.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderLocation(Location $location, $viewType = self::VIEW_TYPE_FULL, $parameters = []);

    /**
     * Renders passed ContentView object via the template engine.
     * If $view's template identifier is a closure, then it is called directly and the result is returned as is.
     *
     * @param array $defaultParams
     *
     * @return string
     */
    public function renderContentView(View $view, array $defaultParams = []);
}

class_alias(ViewManagerInterface::class, 'eZ\Publish\Core\MVC\Symfony\View\ViewManagerInterface');
