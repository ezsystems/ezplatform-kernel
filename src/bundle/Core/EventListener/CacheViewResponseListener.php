<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\View\CachableView;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Configures the Response cache properties.
 */
class CacheViewResponseListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => 'configureCache'];
    }

    public function configureCache(ResponseEvent $event)
    {
        if (!($view = $event->getRequest()->attributes->get('view')) instanceof CachableView) {
            return;
        }

        $isViewCacheEnabled = $this->configResolver->getParameter('content.view_cache');
        if (!$isViewCacheEnabled || !$view->isCacheEnabled()) {
            return;
        }

        $response = $event->getResponse();

        if ($view instanceof LocationValueView && ($location = $view->getLocation()) instanceof Location) {
            $response->headers->set('X-Location-Id', $location->id, false);
        }

        $response->setPublic();

        $isTtlCacheEnabled = $this->configResolver->getParameter('content.ttl_cache');
        if ($isTtlCacheEnabled && !$response->headers->hasCacheControlDirective('s-maxage')) {
            $response->setSharedMaxAge((int) $this->configResolver->getParameter('content.default_ttl'));
        }
    }
}

class_alias(CacheViewResponseListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\CacheViewResponseListener');
