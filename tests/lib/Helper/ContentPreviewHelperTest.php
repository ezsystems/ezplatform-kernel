<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Helper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\Helper\ContentPreviewHelper;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessRouterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContentPreviewHelperTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcher;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $siteAccessRouter;

    /** @var \Ibexa\Core\Helper\ContentPreviewHelper */
    private $previewHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->siteAccessRouter = $this->createMock(SiteAccessRouterInterface::class);
        $this->previewHelper = new ContentPreviewHelper($this->eventDispatcher, $this->siteAccessRouter);
    }

    public function testChangeConfigScope()
    {
        $newSiteAccessName = 'test';
        $newSiteAccess = new SiteAccess($newSiteAccessName);

        $this->siteAccessRouter
            ->expects($this->once())
            ->method('matchByName')
            ->with($this->equalTo($newSiteAccessName))
            ->willReturn($newSiteAccess);

        $event = new ScopeChangeEvent($newSiteAccess);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($event), MVCEvents::CONFIG_SCOPE_CHANGE);

        $originalSiteAccess = new SiteAccess('foo', 'bar');
        $this->previewHelper->setSiteAccess($originalSiteAccess);
        $this->assertEquals(
            $newSiteAccess,
            $this->previewHelper->changeConfigScope($newSiteAccessName)
        );
    }

    public function testRestoreConfigScope()
    {
        $originalSiteAccess = new SiteAccess('foo', 'bar');
        $event = new ScopeChangeEvent($originalSiteAccess);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($event), MVCEvents::CONFIG_SCOPE_RESTORE);

        $this->previewHelper->setSiteAccess($originalSiteAccess);
        $this->assertEquals(
            $originalSiteAccess,
            $this->previewHelper->restoreConfigScope()
        );
    }

    public function testPreviewActive()
    {
        $this->assertFalse($this->previewHelper->isPreviewActive());
        $this->previewHelper->setPreviewActive(true);
        $this->assertTrue($this->previewHelper->isPreviewActive());
        $this->previewHelper->setPreviewActive(false);
        $this->assertFalse($this->previewHelper->isPreviewActive());
    }

    public function testPreviewedContent()
    {
        $this->assertNull($this->previewHelper->getPreviewedContent());
        $content = $this->createMock(APIContent::class);
        $this->previewHelper->setPreviewedContent($content);
        $this->assertSame($content, $this->previewHelper->getPreviewedContent());
    }

    public function testPreviewedLocation()
    {
        $this->assertNull($this->previewHelper->getPreviewedLocation());
        $location = $this->createMock(APILocation::class);
        $this->previewHelper->setPreviewedLocation($location);
        $this->assertSame($location, $this->previewHelper->getPreviewedLocation());
    }
}

class_alias(ContentPreviewHelperTest::class, 'eZ\Publish\Core\Helper\Tests\ContentPreviewHelperTest');
