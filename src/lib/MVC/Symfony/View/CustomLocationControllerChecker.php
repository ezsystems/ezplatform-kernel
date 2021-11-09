<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * Used to check if a Location is rendered using a custom controller.
 */
class CustomLocationControllerChecker
{
    /** @var \Ibexa\Core\MVC\Symfony\View\ViewProvider[] */
    private $viewProviders;

    /**
     * Tests if $location has match a view that uses a custom controller.
     *
     * @since 5.4.5
     *
     * @param $content Content
     * @param $location Location
     * @param $viewMode string
     *
     * @return bool
     */
    public function usesCustomController(Content $content, Location $location, $viewMode = 'full')
    {
        $contentView = new ContentView(null, [], $viewMode);
        $contentView->setContent($content);
        $contentView->setLocation($location);

        foreach ($this->viewProviders as $viewProvider) {
            $view = $viewProvider->getView($contentView);
            if ($view instanceof View) {
                if ($view->getControllerReference() !== null) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  \Ibexa\Core\MVC\Symfony\View\ViewProvider[] $viewProviders
     */
    public function addViewProviders(array $viewProviders)
    {
        $this->viewProviders = $viewProviders;
    }
}

class_alias(CustomLocationControllerChecker::class, 'eZ\Publish\Core\MVC\Symfony\View\CustomLocationControllerChecker');
