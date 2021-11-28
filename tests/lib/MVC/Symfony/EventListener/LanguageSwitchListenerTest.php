<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\EventListener;

use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\Event\RouteReferenceGenerationEvent;
use Ibexa\Core\MVC\Symfony\EventListener\LanguageSwitchListener;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\Routing\RouteReference;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LanguageSwitchListenerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $translationHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [MVCEvents::ROUTE_REFERENCE_GENERATION => 'onRouteReferenceGeneration'],
            LanguageSwitchListener::getSubscribedEvents()
        );
    }

    public function testOnRouteReferenceGenerationNoLanguage()
    {
        $this->translationHelper
            ->expects($this->never())
            ->method('getTranslationSiteAccess');

        $event = new RouteReferenceGenerationEvent(new RouteReference('foo'), new Request());
        $listener = new LanguageSwitchListener($this->translationHelper);
        $listener->onRouteReferenceGeneration($event);
    }

    public function testOnRouteReferenceGeneration()
    {
        $language = 'fre-FR';
        $routeReference = new RouteReference('foo', ['language' => $language]);
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $expectedSiteAccess = 'phoenix_rises';
        $this->translationHelper
            ->expects($this->once())
            ->method('getTranslationSiteAccess')
            ->with($language)
            ->will($this->returnValue($expectedSiteAccess));

        $listener = new LanguageSwitchListener($this->translationHelper);
        $listener->onRouteReferenceGeneration($event);
        $this->assertFalse($routeReference->has('language'));
        $this->assertTrue($routeReference->has('siteaccess'));
        $this->assertSame($expectedSiteAccess, $routeReference->get('siteaccess'));
    }

    public function testOnRouteReferenceGenerationNoTranslationSiteAccess()
    {
        $language = 'fre-FR';
        $routeReference = new RouteReference('foo', ['language' => $language]);
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $this->translationHelper
            ->expects($this->once())
            ->method('getTranslationSiteAccess')
            ->with($language)
            ->will($this->returnValue(null));

        $listener = new LanguageSwitchListener($this->translationHelper);
        $listener->onRouteReferenceGeneration($event);
        $this->assertFalse($routeReference->has('language'));
        $this->assertFalse($routeReference->has('siteaccess'));
    }
}

class_alias(LanguageSwitchListenerTest::class, 'eZ\Publish\Core\MVC\Symfony\EventListener\Tests\LanguageSwitchListenerTest');
