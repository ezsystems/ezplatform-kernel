<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\RoutingListener;
use Ibexa\Bundle\Core\Routing\UrlAliasRouter;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RoutingListenerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $urlAliasRouter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $urlAliasGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->urlAliasRouter = $this->createMock(UrlAliasRouter::class);
        $this->urlAliasGenerator = $this->createMock(UrlAliasGenerator::class);
    }

    public function testGetSubscribedEvents()
    {
        $listener = new RoutingListener($this->configResolver, $this->urlAliasRouter, $this->urlAliasGenerator);
        $this->assertSame(
            [
                MVCEvents::SITEACCESS => ['onSiteAccessMatch', 200],
            ],
            $listener->getSubscribedEvents()
        );
    }

    public function testOnSiteAccessMatch()
    {
        $rootLocationId = 123;
        $excludedUriPrefixes = ['/foo/bar', '/baz'];
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['content.tree_root.location_id', null, null, $rootLocationId],
                        ['content.tree_root.excluded_uri_prefixes', null, null, $excludedUriPrefixes],
                    ]
                )
            );

        $this->urlAliasRouter
            ->expects($this->once())
            ->method('setRootLocationId')
            ->with($rootLocationId);
        $this->urlAliasGenerator
            ->expects($this->once())
            ->method('setRootLocationId')
            ->with($rootLocationId);
        $this->urlAliasGenerator
            ->expects($this->once())
            ->method('setExcludedUriPrefixes')
            ->with($excludedUriPrefixes);

        $event = new PostSiteAccessMatchEvent(new SiteAccess('test'), new Request(), HttpKernelInterface::MASTER_REQUEST);
        $listener = new RoutingListener($this->configResolver, $this->urlAliasRouter, $this->urlAliasGenerator);
        $listener->onSiteAccessMatch($event);
    }
}

class_alias(RoutingListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\RoutingListenerTest');
