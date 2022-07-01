<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\IO\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessService;
use Ibexa\Bundle\IO\EventListener\StreamFileSiteAccessListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class StreamFileSiteAccessListenerTest extends TestCase
{
    /** @var \Ibexa\Bundle\IO\EventListener\StreamFileSiteAccessListener */
    private $eventListener;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $siteAccessRouter;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $siteAccessService;

    /** @var array<string> */
    private $siteAccessList;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->siteAccessRouter = $this->createMock(SiteAccessRouterInterface::class);
        $this->siteAccessService = new SiteAccessService(
            $this->createMock(SiteAccessProviderInterface::class),
            $this->createMock(ConfigResolverInterface::class)
        );
        $this->siteAccessList = [
            'admin',
            'admin2',
            'site',
            'site2',
        ];

        $this->eventListener = new StreamFileSiteAccessListener(
            $this->configResolver,
            $this->siteAccessRouter,
            $this->siteAccessService,
            $this->siteAccessList
        );
    }

    public function testDoNotSwitchSiteAccessIfPathIsNotStoragePath(): void
    {
        $request = Request::create('http://localhost/article');
        $event = $this->createEvent($request);

        $this->configResolver
            ->expects(self::exactly(4))
            ->method('getParameter')
            ->withConsecutive(
                ['var_dir', null, $this->siteAccessList[0]],
                ['var_dir', null, $this->siteAccessList[1]],
                ['var_dir', null, $this->siteAccessList[2]],
                ['var_dir', null, $this->siteAccessList[3]],
            )
            ->willReturnOnConsecutiveCalls(
                'var/site',
                'var/repository2',
                'var/site',
                'var/repository2',
            );

        $this->siteAccessRouter
            ->expects(self::never())
            ->method('matchByName');

        $this->eventListener->onKernelRequest($event);

        self::assertNull($this->siteAccessService->getCurrent());
    }

    public function testSwitchSiteAccessToTheOneFromTheSecondRepository(): void
    {
        $request = Request::create('http://localhost/var/repository2/storage/images/image.png');
        $event = $this->createEvent($request);

        $siteAccessToBeSet = $this->siteAccessList[1];

        $this->configResolver
            ->expects(self::exactly(2))
            ->method('getParameter')
            ->withConsecutive(
                ['var_dir', null, $this->siteAccessList[0]],
                ['var_dir', null, $siteAccessToBeSet],
            )
            ->willReturnOnConsecutiveCalls(
                'var/site',
                'var/repository2',
            );

        $this->siteAccessRouter
            ->expects(self::once())
            ->method('matchByName')
            ->with($siteAccessToBeSet)
            ->willReturn(new SiteAccess($siteAccessToBeSet));

        $this->eventListener->onKernelRequest($event);

        self::assertSame('admin2', $this->siteAccessService->getCurrent()->name);
    }

    protected function createEvent(Request $request): RequestEvent
    {
        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }
}
