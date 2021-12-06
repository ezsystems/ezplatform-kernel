<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * SiteAccess match listener.
 *
 * Allows to set a dynamic session name based on the siteaccess name.
 */
class SessionSetDynamicNameListener implements EventSubscriberInterface
{
    public const MARKER = '{siteaccess_hash}';

    /**
     * Prefix for session name.
     */
    public const SESSION_NAME_PREFIX = 'eZSESSID';

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageFactoryInterface */
    private $sessionStorageFactory;

    public function __construct(
        ConfigResolverInterface $configResolver,
        SessionStorageFactoryInterface $sessionStorageFactory
    ) {
        $this->configResolver = $configResolver;
        $this->sessionStorageFactory = $sessionStorageFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::SITEACCESS => ['onSiteAccessMatch', 250],
        ];
    }

    public function onSiteAccessMatch(PostSiteAccessMatchEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->hasSession() ? $request->getSession() : null;
        $sessionStorage = $this->sessionStorageFactory->createStorage($request);

        if (
            !(
                $event->getRequestType() === HttpKernelInterface::MAIN_REQUEST
                && $session
                && !$session->isStarted()
                && $sessionStorage instanceof NativeSessionStorage
            )
        ) {
            return;
        }

        $sessionOptions = (array)$this->configResolver->getParameter('session');
        $sessionName = isset($sessionOptions['name']) ? $sessionOptions['name'] : $session->getName();
        $sessionOptions['name'] = $this->getSessionName($sessionName, $event->getSiteAccess());
        $sessionStorage->setOptions($sessionOptions);
    }

    /**
     * @param string $sessionName
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $siteAccess
     *
     * @return string
     */
    private function getSessionName($sessionName, SiteAccess $siteAccess)
    {
        // Add session prefix if needed.
        if (strpos($sessionName, static::SESSION_NAME_PREFIX) !== 0) {
            $sessionName = static::SESSION_NAME_PREFIX . '_' . $sessionName;
        }

        // Check if uniqueness marker is present. If so, session name will be unique for current siteaccess.
        if (strpos($sessionName, self::MARKER) !== false) {
            $sessionName = str_replace(self::MARKER, md5($siteAccess->name), $sessionName);
        }

        return $sessionName;
    }
}

class_alias(SessionSetDynamicNameListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\SessionSetDynamicNameListener');
