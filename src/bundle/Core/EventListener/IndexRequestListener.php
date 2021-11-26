<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class IndexRequestListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                // onKernelRequestIndex needs to be before the router (prio 32)
                ['onKernelRequestIndex', 40],
            ],
        ];
    }

    /**
     * Checks if the IndexPage is configured and which page must be shown.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequestIndex(RequestEvent $event)
    {
        $request = $event->getRequest();
        $semanticPathinfo = $request->attributes->get('semanticPathinfo') ?: '/';
        if (
            $event->getRequestType() === HttpKernelInterface::MASTER_REQUEST
            && $semanticPathinfo === '/'
        ) {
            $indexPage = $this->configResolver->getParameter('index_page');
            if ($indexPage !== null) {
                $indexPage = '/' . ltrim($indexPage, '/');
                $request->attributes->set('semanticPathinfo', $indexPage);
                $request->attributes->set('needsRedirect', true);
            }
        }
    }
}

class_alias(IndexRequestListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\IndexRequestListener');
