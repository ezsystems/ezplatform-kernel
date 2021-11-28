<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\View\Renderer;

use Ibexa\Core\MVC\Exception\NoViewTemplateException;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\Renderer\TemplateRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class TemplateRendererTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\View\Renderer\TemplateRenderer */
    private $renderer;

    /** @var \Twig\Environment|\PHPUnit\Framework\MockObject\MockObject */
    private $templateEngineMock;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcherMock;

    protected function setUp(): void
    {
        $this->templateEngineMock = $this->createMock(Environment::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->renderer = new TemplateRenderer(
            $this->templateEngineMock,
            $this->eventDispatcherMock
        );
    }

    public function testRender()
    {
        $view = $this->createView();
        $view->setTemplateIdentifier('path/to/template.html.twig');

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(PreContentViewEvent::class),
                MVCEvents::PRE_CONTENT_VIEW
            );

        $this->templateEngineMock
            ->expects($this->once())
            ->method('render')
            ->with(
                'path/to/template.html.twig',
                $view->getParameters()
            );

        $this->renderer->render($view);
    }

    public function testRenderNoViewTemplate()
    {
        $this->expectException(NoViewTemplateException::class);

        $this->renderer->render($this->createView());
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\View\View
     */
    protected function createView()
    {
        $view = new ContentView();

        return $view;
    }
}

class_alias(TemplateRendererTest::class, 'eZ\Publish\Core\MVC\Symfony\View\Tests\Renderer\TemplateRendererTest');
