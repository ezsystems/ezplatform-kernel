<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\IO\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class StreamFileSiteAccessListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface */
    private $siteAccessRouter;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    /** @var array<string> */
    private $siteAccessList;

    public function __construct(
        ConfigResolverInterface $configResolver,
        SiteAccessRouterInterface $siteAccessRouter,
        SiteAccessServiceInterface $siteAccessService,
        array $siteAccessList
    ) {
        $this->configResolver = $configResolver;
        $this->siteAccessRouter = $siteAccessRouter;
        $this->siteAccessService = $siteAccessService;
        $this->siteAccessList = $siteAccessList;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 43],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $pathInfo = $event->getRequest()->getPathInfo();

        foreach ($this->siteAccessList as $siteAccess) {
            $varDir = $this->configResolver->getParameter('var_dir', null, $siteAccess);
            if (strpos($pathInfo, $varDir) === 1) {
                $this->siteAccessService->setSiteAccess($this->siteAccessRouter->matchByName($siteAccess));
                break;
            }
        }
    }
}
