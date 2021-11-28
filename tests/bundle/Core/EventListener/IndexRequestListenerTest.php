<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\IndexRequestListener;
use Ibexa\Core\MVC\ConfigResolverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class IndexRequestListenerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\Bundle\Core\EventListener\IndexRequestListener */
    private $indexRequestEventListener;

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /** @var \Symfony\Component\HttpKernel\Event\RequestEvent */
    private $event;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $httpKernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configResolver = $this->createMock(ConfigResolverInterface::class);

        $this->indexRequestEventListener = new IndexRequestListener($this->configResolver);

        $this->request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(['getSession', 'hasSession'])
            ->getMock();

        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
        $this->event = new RequestEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST
        );
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            [
                KernelEvents::REQUEST => [
                    ['onKernelRequestIndex', 40],
                ],
            ],
            $this->indexRequestEventListener->getSubscribedEvents()
        );
    }

    /**
     * @dataProvider indexPageProvider
     */
    public function testOnKernelRequestIndexOnIndexPage($requestPath, $configuredIndexPath, $expectedIndexPath)
    {
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('index_page')
            ->will($this->returnValue($configuredIndexPath));
        $this->request->attributes->set('semanticPathinfo', $requestPath);
        $this->indexRequestEventListener->onKernelRequestIndex($this->event);
        $this->assertEquals($expectedIndexPath, $this->request->attributes->get('semanticPathinfo'));
        $this->assertTrue($this->request->attributes->get('needsRedirect'));
    }

    public function indexPageProvider()
    {
        return [
            ['/', '/foo', '/foo'],
            ['/', '/foo/', '/foo/'],
            ['/', '/foo/bar', '/foo/bar'],
            ['/', 'foo/bar', '/foo/bar'],
            ['', 'foo/bar', '/foo/bar'],
            ['', '/foo/bar', '/foo/bar'],
            ['', '/foo/bar/', '/foo/bar/'],
        ];
    }

    public function testOnKernelRequestIndexNotOnIndexPage()
    {
        $this->request->attributes->set('semanticPathinfo', '/anyContent');
        $this->indexRequestEventListener->onKernelRequestIndex($this->event);
        $this->assertFalse($this->request->attributes->has('needsRedirect'));
    }
}

class_alias(IndexRequestListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\IndexRequestListenerTest');
