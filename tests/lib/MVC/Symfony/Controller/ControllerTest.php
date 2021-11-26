<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Controller;

use Ibexa\Core\MVC\Symfony\Controller\Controller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @covers \Ibexa\Core\MVC\Symfony\Controller\Controller::render
 *
 * @mvc
 */
class ControllerTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\Controller\Controller */
    protected $controller;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $templateEngineMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $containerMock;

    protected function setUp(): void
    {
        $this->templateEngineMock = $this->createMock(EngineInterface::class);
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->controller = $this->getMockForAbstractClass(Controller::class);
        $this->controller->setContainer($this->containerMock);
        $this->containerMock
            ->expects($this->any())
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($this->templateEngineMock));
    }

    public function testRender()
    {
        $view = 'some:valid:view.html.twig';
        $params = ['foo' => 'bar', 'truc' => 'muche'];
        $tplResult = "I'm a template result";
        $this->templateEngineMock
            ->expects($this->once())
            ->method('render')
            ->with($view, $params)
            ->will($this->returnValue($tplResult));
        $response = $this->controller->render($view, $params);
        self::assertInstanceOf(Response::class, $response);
        self::assertSame($tplResult, $response->getContent());
    }

    public function testRenderWithResponse()
    {
        $response = new Response();
        $view = 'some:valid:view.html.twig';
        $params = ['foo' => 'bar', 'truc' => 'muche'];
        $tplResult = "I'm a template result";
        $this->templateEngineMock
            ->expects($this->once())
            ->method('render')
            ->with($view, $params)
            ->will($this->returnValue($tplResult));

        self::assertSame($response, $this->controller->render($view, $params, $response));
        self::assertSame($tplResult, $response->getContent());
    }
}

class_alias(ControllerTest::class, 'eZ\Publish\Core\MVC\Symfony\Controller\Tests\ControllerTest');
