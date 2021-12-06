<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Helper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContentPreviewHelper implements SiteAccessAware
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface */
    protected $siteAccessRouter;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    protected $originalSiteAccess;

    /** @var bool */
    private $previewActive = false;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $previewedContent;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $previewedLocation;

    public function __construct(EventDispatcherInterface $eventDispatcher, SiteAccessRouterInterface $siteAccessRouter)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->siteAccessRouter = $siteAccessRouter;
    }

    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->originalSiteAccess = $siteAccess;
    }

    /**
     * Return original SiteAccess.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function getOriginalSiteAccess()
    {
        return $this->originalSiteAccess;
    }

    /**
     * Switches configuration scope to $siteAccessName and returns the new SiteAccess to use for preview.
     *
     * @param string $siteAccessName
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function changeConfigScope($siteAccessName)
    {
        $event = new ScopeChangeEvent($this->siteAccessRouter->matchByName($siteAccessName));
        $this->eventDispatcher->dispatch($event, MVCEvents::CONFIG_SCOPE_CHANGE);

        return $event->getSiteAccess();
    }

    /**
     * Restores original config scope.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     */
    public function restoreConfigScope()
    {
        $event = new ScopeChangeEvent($this->originalSiteAccess);
        $this->eventDispatcher->dispatch($event, MVCEvents::CONFIG_SCOPE_RESTORE);

        return $event->getSiteAccess();
    }

    /**
     * @return bool
     */
    public function isPreviewActive()
    {
        return $this->previewActive;
    }

    /**
     * @param bool $previewActive
     */
    public function setPreviewActive($previewActive)
    {
        $this->previewActive = (bool)$previewActive;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function getPreviewedContent()
    {
        return $this->previewedContent;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $previewedContent
     */
    public function setPreviewedContent(Content $previewedContent)
    {
        $this->previewedContent = $previewedContent;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getPreviewedLocation()
    {
        return $this->previewedLocation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $previewedLocation
     */
    public function setPreviewedLocation(Location $previewedLocation)
    {
        $this->previewedLocation = $previewedLocation;
    }
}

class_alias(ContentPreviewHelper::class, 'eZ\Publish\Core\Helper\ContentPreviewHelper');
