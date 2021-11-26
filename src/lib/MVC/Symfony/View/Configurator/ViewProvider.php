<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\Configurator;

use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\Provider\Registry;
use Ibexa\Core\MVC\Symfony\View\View;

/**
 * Configures a view based on the ViewProviders.
 *
 * Typically, the Configured ViewProvider will be included, meaning that Views will be customized based on the
 * view rules defined in the siteaccess aware configuration (content_view, block_view, ...).
 */
class ViewProvider implements Configurator
{
    /** @var \Ibexa\Core\MVC\Symfony\View\Provider\Registry */
    private $providerRegistry;

    /**
     * ViewProvider constructor.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\Provider\Registry $providersRegistry
     */
    public function __construct(Registry $providersRegistry)
    {
        $this->providerRegistry = $providersRegistry;
    }

    public function configure(View $view)
    {
        foreach ($this->providerRegistry->getViewProviders($view) as $viewProvider) {
            if ($providerView = $viewProvider->getView($view)) {
                $view->setConfigHash($providerView->getConfigHash());
                if (($templateIdentifier = $providerView->getTemplateIdentifier()) !== null) {
                    $view->setTemplateIdentifier($templateIdentifier);
                }

                if (($controllerReference = $providerView->getControllerReference()) !== null) {
                    $view->setControllerReference($controllerReference);
                }

                $view->addParameters($providerView->getParameters());

                return;
            }
        }
    }
}

class_alias(ViewProvider::class, 'eZ\Publish\Core\MVC\Symfony\View\Configurator\ViewProvider');
