<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\SessionSetDynamicNameListener;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface as SymfonySessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SessionSetDynamicNameListenerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $sessionStorageFactory;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $sessionStorage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configResolver = $this->getMockBuilder(ConfigResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->getMockBuilder(SymfonySessionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionStorage = $this->getMockBuilder(NativeSessionStorage::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionStorageFactory = $this->getMockBuilder(SessionStorageFactoryInterface::class)
            ->getMock();
        $this->sessionStorageFactory->method('createStorage')
            ->willReturn($this->sessionStorage);
    }

    public function testGetSubscribedEvents()
    {
        $listener = new SessionSetDynamicNameListener($this->configResolver, $this->sessionStorageFactory);
        $this->assertSame(
            [
                MVCEvents::SITEACCESS => ['onSiteAccessMatch', 250],
            ],
            $listener->getSubscribedEvents()
        );
    }

    public function testOnSiteAccessMatchNoSession()
    {
        $request = new Request();

        $this->sessionStorage
            ->expects($this->never())
            ->method('setOptions');
        $listener = new SessionSetDynamicNameListener($this->configResolver, $this->sessionStorageFactory);
        $listener->onSiteAccessMatch(new PostSiteAccessMatchEvent(new SiteAccess('test'), $request, HttpKernelInterface::MASTER_REQUEST));
    }

    public function testOnSiteAccessMatchSubRequest()
    {
        $this->sessionStorage
            ->expects($this->never())
            ->method('setOptions');
        $listener = new SessionSetDynamicNameListener($this->configResolver, $this->sessionStorageFactory);
        $listener->onSiteAccessMatch(new PostSiteAccessMatchEvent(new SiteAccess('test'), new Request(), HttpKernelInterface::SUB_REQUEST));
    }

    public function testOnSiteAccessMatchNonNativeSessionStorage()
    {
        $this->configResolver
            ->expects($this->never())
            ->method('getParameter');
        $listener = new SessionSetDynamicNameListener(
            $this->configResolver,
            $this->createMock(SessionStorageFactoryInterface::class)
        );
        $listener->onSiteAccessMatch(new PostSiteAccessMatchEvent(new SiteAccess('test'), new Request(), HttpKernelInterface::SUB_REQUEST));
    }

    /**
     * @dataProvider onSiteAccessMatchProvider
     */
    public function testOnSiteAccessMatch(SiteAccess $siteAccess, $configuredSessionStorageOptions, array $expectedSessionStorageOptions)
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $this->sessionStorage
            ->expects($this->once())
            ->method('setOptions')
            ->with($expectedSessionStorageOptions);
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('session')
            ->will($this->returnValue($configuredSessionStorageOptions));

        $listener = new SessionSetDynamicNameListener($this->configResolver, $this->sessionStorageFactory);
        $listener->onSiteAccessMatch(new PostSiteAccessMatchEvent($siteAccess, $request, HttpKernelInterface::MAIN_REQUEST));
    }

    public function onSiteAccessMatchProvider()
    {
        return [
            [new SiteAccess('foo'), ['name' => 'eZSESSID'], ['name' => 'eZSESSID']],
            [new SiteAccess('foo'), ['name' => 'eZSESSID{siteaccess_hash}'], ['name' => 'eZSESSID' . md5('foo')]],
            [new SiteAccess('foo'), ['name' => 'this_is_a_session_name'], ['name' => 'eZSESSID_this_is_a_session_name']],
            [new SiteAccess('foo'), ['name' => 'something{siteaccess_hash}'], ['name' => 'eZSESSID_something' . md5('foo')]],
            [new SiteAccess('bar_baz'), ['name' => '{siteaccess_hash}something'], ['name' => 'eZSESSID_' . md5('bar_baz') . 'something']],
            [
                new SiteAccess('foo'),
                [
                    'name' => 'this_is_a_session_name',
                    'cookie_path' => '/foo',
                    'cookie_domain' => 'foo.com',
                    'cookie_lifetime' => 86400,
                    'cookie_secure' => false,
                    'cookie_httponly' => true,
                ],
                [
                    'name' => 'eZSESSID_this_is_a_session_name',
                    'cookie_path' => '/foo',
                    'cookie_domain' => 'foo.com',
                    'cookie_lifetime' => 86400,
                    'cookie_secure' => false,
                    'cookie_httponly' => true,
                ],
            ],
        ];
    }

    public function testOnSiteAccessMatchNoConfiguredSessionName()
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage('some_default_name')));

        $configuredSessionStorageOptions = ['cookie_path' => '/bar'];
        $sessionName = 'some_default_name';
        $sessionOptions = $configuredSessionStorageOptions + ['name' => "eZSESSID_$sessionName"];

        $this->sessionStorage
            ->expects($this->once())
            ->method('setOptions')
            ->with($sessionOptions);
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('session')
            ->will($this->returnValue($configuredSessionStorageOptions));

        $listener = new SessionSetDynamicNameListener($this->configResolver, $this->sessionStorageFactory);
        $listener->onSiteAccessMatch(new PostSiteAccessMatchEvent(new SiteAccess('test'), $request, HttpKernelInterface::MAIN_REQUEST));
    }
}

class_alias(SessionSetDynamicNameListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\SessionSetDynamicNameListenerTest');
