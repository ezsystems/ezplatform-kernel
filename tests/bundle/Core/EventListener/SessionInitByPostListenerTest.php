<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\SessionInitByPostListener;
use Ibexa\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SessionInitByPostListenerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Core\EventListener\SessionInitByPostListener */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new SessionInitByPostListener();
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [
                MVCEvents::SITEACCESS => ['onSiteAccessMatch', 249],
            ],
            SessionInitByPostListener::getSubscribedEvents()
        );
    }

    public function testOnSiteAccessMatchNoSessionService()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $event = new PostSiteAccessMatchEvent(new SiteAccess('test'), $request, HttpKernelInterface::MAIN_REQUEST);
        $listener = new SessionInitByPostListener();
        $this->assertNull($listener->onSiteAccessMatch($event));
    }

    public function testOnSiteAccessMatchSubRequest()
    {
        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->never())
            ->method('getName');

        $request = new Request();
        $request->setSession($session);

        $event = new PostSiteAccessMatchEvent(new SiteAccess('test'), $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onSiteAccessMatch($event);
    }

    public function testOnSiteAccessMatchRequestNoSessionName()
    {
        $sessionName = 'eZSESSID';

        $session = $this->createMock(SessionInterface::class);
        $session
            ->method('getName')
            ->will($this->returnValue($sessionName));
        $session
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));
        $session
            ->expects($this->never())
            ->method('setId');
        $session
            ->expects($this->never())
            ->method('start');

        $request = new Request();
        $request->setSession($session);

        $event = new PostSiteAccessMatchEvent(new SiteAccess('test'), $request, HttpKernelInterface::MAIN_REQUEST);

        $this->listener->onSiteAccessMatch($event);
    }

    public function testOnSiteAccessMatchNewSessionName()
    {
        $sessionName = 'eZSESSID';
        $sessionId = 'foobar123';
        $session = $this->createMock(SessionInterface::class);

        $session
            ->method('getName')
            ->will($this->returnValue($sessionName));
        $session
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));
        $session
            ->expects($this->once())
            ->method('setId')
            ->with($sessionId);
        $session
            ->expects($this->once())
            ->method('start');

        $request = new Request();
        $request->setSession($session);
        $request->request->set($sessionName, $sessionId);
        $event = new PostSiteAccessMatchEvent(new SiteAccess('test'), $request, HttpKernelInterface::MAIN_REQUEST);

        $this->listener->onSiteAccessMatch($event);
    }
}

class_alias(SessionInitByPostListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\SessionInitByPostListenerTest');
