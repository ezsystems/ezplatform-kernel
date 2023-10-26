<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Initializes the session id by looking at a POST variable named like the
 * session. Mainly used by Flash (for instance ezmultiupload LS).
 */
class SessionInitByPostListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::SITEACCESS => ['onSiteAccessMatch', 249],
        ];
    }

    public function onSiteAccessMatch(PostSiteAccessMatchEvent $event)
    {
        $request = $event->getRequest();
        $request->hasSession() ? $session = $request->getSession() : $session = false;

        if (!$session || $event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $sessionName = $session->getName();
        $request = $event->getRequest();

        if (
            !$session->isStarted()
            && !$request->hasPreviousSession()
            && $request->request->has($sessionName)
        ) {
            $session->setId($request->request->get($sessionName));
            $session->start();
        }
    }
}
