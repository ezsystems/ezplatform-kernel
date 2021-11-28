<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\ViewControllerListener;
use Ibexa\Contracts\Core\Event\View\PostBuildViewEvent;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilderRegistry;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewControllerListenerTest extends TestCase
{
    /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $controllerResolver;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \Ibexa\Bundle\Core\EventListener\ViewControllerListener */
    private $controllerListener;

    /** @var \Symfony\Component\HttpKernel\Event\ControllerEvent */
    private $event;

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /** @var \Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilderRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $viewBuilderRegistry;

    /** @var \Ibexa\Core\MVC\Symfony\View\Configurator|\PHPUnit\Framework\MockObject\MockObject */
    private $viewConfigurator;

    /** @var \Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder|\PHPUnit\Framework\MockObject\MockObject */
    private $viewBuilderMock;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controllerResolver = $this->createMock(ControllerResolverInterface::class);
        $this->viewBuilderRegistry = $this->createMock(ViewBuilderRegistry::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->controllerListener = new ViewControllerListener(
            $this->controllerResolver,
            $this->viewBuilderRegistry,
            $this->eventDispatcher,
            $this->logger
        );

        $this->request = new Request();
        $this->event = $this->createEvent();

        $this->viewBuilderMock = $this->createMock(ViewBuilder::class);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [KernelEvents::CONTROLLER => ['getController', 10]],
            $this->controllerListener::getSubscribedEvents()
        );
    }

    public function testGetControllerNoBuilder()
    {
        $initialController = 'Foo::bar';
        $this->request->attributes->set('_controller', $initialController);

        $this->viewBuilderRegistry
            ->expects($this->once())
            ->method('getFromRegistry')
            ->with('Foo::bar')
            ->willReturn(null);

        $this->controllerListener->getController($this->event);
    }

    public function testGetControllerWithClosure()
    {
        $initialController = static function () {};
        $this->request->attributes->set('_controller', $initialController);

        $this->viewBuilderRegistry
            ->expects($this->once())
            ->method('getFromRegistry')
            ->with($initialController)
            ->willReturn(null);

        $this->controllerListener->getController($this->event);
    }

    public function testGetControllerMatchedView()
    {
        $contentId = 12;
        $locationId = 123;
        $viewType = 'full';

        $templateIdentifier = 'FooBundle:full:template.twig.html';
        $customController = 'FooBundle::bar';

        $this->request->attributes->add(
            [
                '_controller' => 'ez_content:viewAction',
                'contentId' => $contentId,
                'locationId' => $locationId,
                'viewType' => $viewType,
            ]
        );

        $this->viewBuilderRegistry
            ->expects($this->once())
            ->method('getFromRegistry')
            ->will($this->returnValue($this->viewBuilderMock));

        $viewObject = new ContentView($templateIdentifier);
        $viewObject->setControllerReference(new ControllerReference($customController));

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->will($this->returnValue($viewObject));

        $this->controllerResolver
            ->expects($this->once())
            ->method('getController')
            ->will($this->returnValue(static function () {}));

        $this->controllerListener->getController($this->event);
        $this->assertEquals($customController, $this->request->attributes->get('_controller'));

        $expectedView = new ContentView();
        $expectedView->setTemplateIdentifier($templateIdentifier);
        $expectedView->setControllerReference(new ControllerReference($customController));

        $this->assertEquals($expectedView, $this->request->attributes->get('view'));
    }

    public function testGetControllerEmitsProperEvents(): void
    {
        $viewObject = new class() extends BaseView {
        };

        $this->viewBuilderRegistry
            ->expects($this->once())
            ->method('getFromRegistry')
            ->willReturn($this->viewBuilderMock);

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->willReturn($viewObject);

        $this->eventDispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [
                    $this->isInstanceOf(FilterViewBuilderParametersEvent::class),
                    $this->identicalTo(ViewEvents::FILTER_BUILDER_PARAMETERS),
                ],
                [
                    $this->isInstanceOf(PostBuildViewEvent::class),
                    $this->isNull(),
                ]
            )
            ->willReturnArgument(0);

        $this->controllerListener->getController($this->event);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Event\ControllerEvent
     */
    protected function createEvent()
    {
        return new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            static function () {},
            $this->request,
            HttpKernelInterface::MASTER_REQUEST
        );
    }
}

class_alias(ViewControllerListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\ViewControllerListenerTest');
