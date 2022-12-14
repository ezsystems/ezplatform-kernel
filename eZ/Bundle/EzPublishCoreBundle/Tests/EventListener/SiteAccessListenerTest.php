<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\EventListener;

use eZ\Bundle\EzPublishCoreBundle\EventListener\SiteAccessListener;
use eZ\Publish\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccessGroup;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SiteAccessListenerTest extends TestCase
{
    /** @var \eZ\Bundle\EzPublishCoreBundle\EventListener\SiteAccessListener */
    private $listener;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $defaultSiteaccess;

    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultSiteaccess = new SiteAccess('default');
        $this->defaultSiteaccess->groups = [new SiteAccessGroup('test_group')];
        $this->listener = new SiteAccessListener($this->defaultSiteaccess);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [
                MVCEvents::SITEACCESS => ['onSiteAccessMatch', 255],
            ],
            $this->listener->getSubscribedEvents()
        );
    }

    public function siteAccessMatchProvider()
    {
        return [
            ['/foo/bar', '/foo/bar', '', []],
            ['/my_siteaccess/foo/bar', '/foo/bar', '', []],
            ['/foo/bar/(some)/thing', '/foo/bar', '/(some)/thing', ['some' => 'thing']],
            ['/foo/bar/(some)/thing/(other)', '/foo/bar', '/(some)/thing/(other)', ['some' => 'thing', 'other' => '']],
            ['/foo/bar/(some)/thing/orphan', '/foo/bar', '/(some)/thing/orphan', ['some' => 'thing/orphan']],
            ['/foo/bar/(some)/thing//orphan', '/foo/bar', '/(some)/thing//orphan', ['some' => 'thing/orphan']],
            ['/foo/bar/(some)/thing/orphan/(something)/else', '/foo/bar', '/(some)/thing/orphan/(something)/else', ['some' => 'thing/orphan', 'something' => 'else']],
            ['/foo/bar/(some)/thing/orphan/(something)/else/(other)', '/foo/bar', '/(some)/thing/orphan/(something)/else/(other)', ['some' => 'thing/orphan', 'something' => 'else', 'other' => '']],
            ['/foo/bar/(some)/thing/orphan/(other)', '/foo/bar', '/(some)/thing/orphan/(other)', ['some' => 'thing/orphan', 'other' => '']],
            ['/my_siteaccess/foo/bar/(some)/thing', '/foo/bar', '/(some)/thing', ['some' => 'thing']],
            ['/foo/bar/(some)/thing/(toto_titi)/tata_tutu', '/foo/bar', '/(some)/thing/(toto_titi)/tata_tutu', ['some' => 'thing', 'toto_titi' => 'tata_tutu']],
            ['/foo/%E8%B5%A4/%28some%29/thing', '/foo/赤', '/(some)/thing', ['some' => 'thing']],
        ];
    }

    /**
     * @dataProvider siteAccessMatchProvider
     */
    public function testOnSiteAccessMatchMasterRequest(
        $uri,
        $expectedSemanticPathinfo,
        $expectedVPString,
        array $expectedVPArray
    ) {
        $uri = rawurldecode($uri);
        $semanticPathinfoPos = strpos($uri, $expectedSemanticPathinfo);
        if ($semanticPathinfoPos !== 0) {
            $semanticPathinfo = substr($uri, $semanticPathinfoPos);
            $matcher = $this->createMock(SiteAccess\URILexer::class);
            $matcher
                ->expects($this->once())
                ->method('analyseURI')
                ->with($uri)
                ->will($this->returnValue($semanticPathinfo));
        } else {
            $matcher = $this->createMock(SiteAccess\Matcher::class);
        }

        $siteAccess = new SiteAccess('test', 'test', $matcher);
        $request = Request::create($uri);
        $event = new PostSiteAccessMatchEvent($siteAccess, $request, HttpKernelInterface::MASTER_REQUEST);

        $this->listener->onSiteAccessMatch($event);
        self::assertSame($expectedSemanticPathinfo, $request->attributes->get('semanticPathinfo'));
        self::assertSame($expectedVPArray, $request->attributes->get('viewParameters'));
        self::assertSame($expectedVPString, $request->attributes->get('viewParametersString'));
        self::assertSame($this->defaultSiteaccess->name, $siteAccess->name);
        self::assertSame($this->defaultSiteaccess->matchingType, $siteAccess->matchingType);
        self::assertSame($this->defaultSiteaccess->matcher, $siteAccess->matcher);
        self::assertSame($this->defaultSiteaccess->groups, $siteAccess->groups);
    }

    /**
     * @dataProvider siteAccessMatchProvider
     */
    public function testOnSiteAccessMatchSubRequest($uri, $semanticPathinfo, $vpString, $expectedViewParameters)
    {
        $siteAccess = new SiteAccess('test', 'test', $this->createMock(SiteAccess\Matcher::class));
        $request = Request::create($uri);
        $request->attributes->set('semanticPathinfo', $semanticPathinfo);
        if (!empty($vpString)) {
            $request->attributes->set('viewParametersString', $vpString);
        }
        $event = new PostSiteAccessMatchEvent($siteAccess, $request, HttpKernelInterface::SUB_REQUEST);

        $this->listener->onSiteAccessMatch($event);
        self::assertSame($semanticPathinfo, $request->attributes->get('semanticPathinfo'));
        self::assertSame($expectedViewParameters, $request->attributes->get('viewParameters'));
        self::assertSame($vpString, $request->attributes->get('viewParametersString'));
        self::assertSame($this->defaultSiteaccess->name, $siteAccess->name);
        self::assertSame($this->defaultSiteaccess->matchingType, $siteAccess->matchingType);
        self::assertSame($this->defaultSiteaccess->matcher, $siteAccess->matcher);
        self::assertSame($this->defaultSiteaccess->groups, $siteAccess->groups);
    }
}
