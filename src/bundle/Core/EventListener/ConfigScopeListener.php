<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Ibexa\Core\MVC\Symfony\Configuration\VersatileScopeInterface;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\Core\MVC\Symfony\View\ViewManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigScopeListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface[] */
    private $configResolvers;

    /** @var \Ibexa\Core\MVC\Symfony\View\ViewManagerInterface|\Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware */
    private $viewManager;

    /** @var \Ibexa\Core\MVC\Symfony\View\ViewProvider[]|\Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware[] */
    private $viewProviders;

    public function __construct(
        iterable $configResolvers,
        ViewManagerInterface $viewManager
    ) {
        $this->configResolvers = $configResolvers;
        $this->viewManager = $viewManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::CONFIG_SCOPE_CHANGE => ['onConfigScopeChange', 100],
            MVCEvents::CONFIG_SCOPE_RESTORE => ['onConfigScopeChange', 100],
        ];
    }

    public function onConfigScopeChange(ScopeChangeEvent $event)
    {
        $siteAccess = $event->getSiteAccess();

        foreach ($this->configResolvers as $configResolver) {
            if ($configResolver instanceof VersatileScopeInterface) {
                $configResolver->setDefaultScope($siteAccess->name);
            }
        }

        if ($this->viewManager instanceof SiteAccessAware) {
            $this->viewManager->setSiteAccess($siteAccess);
        }

        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider instanceof SiteAccessAware) {
                $viewProvider->setSiteAccess($siteAccess);
            }
        }
    }

    /**
     * Sets the complete list of view providers.
     */
    public function setViewProviders(array $viewProviders)
    {
        $this->viewProviders = $viewProviders;
    }
}

class_alias(ConfigScopeListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\ConfigScopeListener');
