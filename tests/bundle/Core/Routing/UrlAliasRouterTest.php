<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Routing;

use Ibexa\Bundle\Core\Routing\UrlAliasRouter;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\Core\MVC\Symfony\View\Manager as ViewManager;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Tests\Core\MVC\Symfony\Routing\UrlAliasRouterTest as BaseUrlAliasRouterTest;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RequestContext;

class UrlAliasRouterTest extends BaseUrlAliasRouterTest
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                        ['content.tree_root.location_id', null, null, null],
                        ['content.tree_root.excluded_uri_prefixes', null, null, []],
                    ]
                )
            );
        parent::setUp();
    }

    protected function getRouter(LocationService $locationService, URLAliasService $urlAliasService, ContentService $contentService, UrlAliasGenerator $urlAliasGenerator, RequestContext $requestContext)
    {
        $router = new UrlAliasRouter($locationService, $urlAliasService, $contentService, $urlAliasGenerator, $requestContext);
        $router->setConfigResolver($this->configResolver);

        return $router;
    }

    /**
     * Resets container and configResolver mocks.
     */
    protected function resetConfigResolver()
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->router->setConfigResolver($this->configResolver);
    }

    public function testMatchRequestDeactivatedUrlAlias()
    {
        $this->expectException(\Symfony\Component\Routing\Exception\ResourceNotFoundException::class);

        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, false],
                    ]
                )
            );
        $this->router->matchRequest($this->getRequestByPathInfo('/foo'));
    }

    public function testMatchRequestWithRootLocation()
    {
        $rootLocationId = 123;
        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                    ]
                )
            );
        $this->router->setRootLocationId($rootLocationId);

        $prefix = '/root/prefix';
        $this->urlALiasGenerator
            ->expects($this->exactly(2))
            ->method('getPathPrefixByRootLocationId')
            ->with($rootLocationId)
            ->will($this->returnValue($prefix));

        $locationId = 789;
        $path = '/foo/bar';
        $urlAlias = new URLAlias(
            [
                'destination' => $locationId,
                'path' => $prefix . $path,
                'type' => URLAlias::LOCATION,
                'isHistory' => false,
            ]
        );
        $this->urlAliasService
            ->expects($this->once())
            ->method('lookup')
            ->with($prefix . $path)
            ->will($this->returnValue($urlAlias));

        $this->urlALiasGenerator
            ->expects($this->once())
            ->method('loadLocation')
            ->will($this->returnValue(new Location(['contentInfo' => new ContentInfo(['id' => 456])])));

        $expected = [
            '_route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            '_controller' => UrlAliasRouter::VIEW_ACTION,
            'locationId' => $locationId,
            'contentId' => 456,
            'viewType' => ViewManager::VIEW_TYPE_FULL,
            'layout' => true,
        ];
        $request = $this->getRequestByPathInfo($path);
        $this->assertEquals($expected, $this->router->matchRequest($request));
    }

    public function testMatchRequestLocationCaseRedirectWithRootLocation()
    {
        $rootLocationId = 123;
        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                    ]
                )
            );
        $this->router->setRootLocationId($rootLocationId);

        $prefix = '/root/prefix';
        $this->urlALiasGenerator
            ->expects($this->exactly(2))
            ->method('getPathPrefixByRootLocationId')
            ->with($rootLocationId)
            ->will($this->returnValue($prefix));
        $this->urlALiasGenerator
            ->expects($this->once())
            ->method('loadLocation')
            ->will($this->returnValue(new Location(['contentInfo' => new ContentInfo(['id' => 456])])));

        $locationId = 789;
        $path = '/foo/bar';
        $requestedPath = '/Foo/Bar';
        $urlAlias = new URLAlias(
            [
                'destination' => $locationId,
                'path' => $prefix . $path,
                'type' => URLAlias::LOCATION,
                'isHistory' => false,
            ]
        );
        $this->urlAliasService
            ->expects($this->once())
            ->method('lookup')
            ->with($prefix . $requestedPath)
            ->will($this->returnValue($urlAlias));

        $expected = [
            '_route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            '_controller' => UrlAliasRouter::VIEW_ACTION,
            'locationId' => $locationId,
            'contentId' => 456,
            'viewType' => ViewManager::VIEW_TYPE_FULL,
            'layout' => true,
            'semanticPathinfo' => $path,
            'needsRedirect' => true,
        ];
        $request = $this->getRequestByPathInfo($requestedPath);
        $this->assertEquals($expected, $this->router->matchRequest($request));
    }

    public function testMatchRequestLocationCaseRedirectWithRootRootLocation()
    {
        $rootLocationId = 123;
        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                    ]
                )
            );
        $this->router->setRootLocationId($rootLocationId);

        $prefix = '/';
        $this->urlALiasGenerator
            ->expects($this->exactly(2))
            ->method('getPathPrefixByRootLocationId')
            ->with($rootLocationId)
            ->will($this->returnValue($prefix));

        $locationId = 789;
        $path = '/foo/bar';
        $requestedPath = '/Foo/Bar';
        $urlAlias = new URLAlias(
            [
                'destination' => $locationId,
                'path' => $path,
                'type' => URLAlias::LOCATION,
                'isHistory' => false,
            ]
        );
        $this->urlAliasService
            ->expects($this->once())
            ->method('lookup')
            ->with($requestedPath)
            ->will($this->returnValue($urlAlias));
        $this->urlALiasGenerator
            ->expects($this->once())
            ->method('loadLocation')
            ->will($this->returnValue(new Location(['contentInfo' => new ContentInfo(['id' => 456])])));

        $expected = [
            '_route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            '_controller' => UrlAliasRouter::VIEW_ACTION,
            'locationId' => $locationId,
            'contentId' => 456,
            'viewType' => ViewManager::VIEW_TYPE_FULL,
            'layout' => true,
            'semanticPathinfo' => $path,
            'needsRedirect' => true,
        ];
        $request = $this->getRequestByPathInfo($requestedPath);
        $this->assertEquals($expected, $this->router->matchRequest($request));
    }

    public function testMatchRequestResourceCaseRedirectWithRootLocation()
    {
        $rootLocationId = 123;
        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                    ]
                )
            );
        $this->router->setRootLocationId($rootLocationId);

        $prefix = '/root/prefix';
        $this->urlALiasGenerator
            ->expects($this->exactly(2))
            ->method('getPathPrefixByRootLocationId')
            ->with($rootLocationId)
            ->will($this->returnValue($prefix));

        $path = '/foo/bar';
        $requestedPath = '/Foo/Bar';
        $urlAlias = new URLAlias(
            [
                'destination' => '/content/search',
                'path' => $prefix . $path,
                'type' => URLAlias::RESOURCE,
                'isHistory' => false,
            ]
        );
        $this->urlAliasService
            ->expects($this->once())
            ->method('lookup')
            ->with($prefix . $requestedPath)
            ->will($this->returnValue($urlAlias));

        $expected = [
            '_route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            'semanticPathinfo' => $path,
            'needsRedirect' => true,
        ];
        $request = $this->getRequestByPathInfo($requestedPath);
        $this->assertEquals($expected, $this->router->matchRequest($request));
    }

    public function testMatchRequestVirtualCaseRedirectWithRootLocation()
    {
        $rootLocationId = 123;
        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                    ]
                )
            );
        $this->router->setRootLocationId($rootLocationId);

        $prefix = '/root/prefix';
        $this->urlALiasGenerator
            ->expects($this->exactly(2))
            ->method('getPathPrefixByRootLocationId')
            ->with($rootLocationId)
            ->will($this->returnValue($prefix));

        $path = '/foo/bar';
        $requestedPath = '/Foo/Bar';
        $urlAlias = new URLAlias(
            [
                'path' => $prefix . $path,
                'type' => URLAlias::VIRTUAL,
            ]
        );
        $this->urlAliasService
            ->expects($this->once())
            ->method('lookup')
            ->with($prefix . $requestedPath)
            ->will($this->returnValue($urlAlias));

        $expected = [
            '_route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            'semanticPathinfo' => $path,
            'needsRedirect' => true,
        ];
        $request = $this->getRequestByPathInfo($requestedPath);
        $this->assertEquals($expected, $this->router->matchRequest($request));
    }

    public function testMatchRequestWithRootLocationAndExclusion()
    {
        $this->resetConfigResolver();
        $this->configResolver
            ->expects($this->any())
            ->method('getParameter')
            ->will(
                $this->returnValueMap(
                    [
                        ['url_alias_router', null, null, true],
                        ['content.tree_root.location_id', null, null, 123],
                        ['content.tree_root.excluded_uri_prefixes', null, null, ['/shared/content']],
                    ]
                )
            );
        $this->router->setRootLocationId(123);

        $pathInfo = '/shared/content/foo-bar';
        $destinationId = 789;
        $this->urlALiasGenerator
            ->expects($this->any())
            ->method('isUriPrefixExcluded')
            ->with($pathInfo)
            ->will($this->returnValue(true));

        $urlAlias = new URLAlias(
            [
                'path' => $pathInfo,
                'type' => UrlAlias::LOCATION,
                'destination' => $destinationId,
                'isHistory' => false,
            ]
        );
        $request = $this->getRequestByPathInfo($pathInfo);
        $this->urlAliasService
            ->expects($this->once())
            ->method('lookup')
            ->with($pathInfo)
            ->will($this->returnValue($urlAlias));
        $this->urlALiasGenerator
            ->expects($this->once())
            ->method('loadLocation')
            ->will($this->returnValue(new Location(['contentInfo' => new ContentInfo(['id' => 456])])));

        $expected = [
            '_route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            '_controller' => UrlAliasRouter::VIEW_ACTION,
            'locationId' => $destinationId,
            'contentId' => 456,
            'viewType' => ViewManager::VIEW_TYPE_FULL,
            'layout' => true,
        ];
        $this->assertEquals($expected, $this->router->matchRequest($request));
    }
}

class_alias(UrlAliasRouterTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Routing\UrlAliasRouterTest');
