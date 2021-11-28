<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\View;

use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\MVC\Symfony\View\View;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\MVC\Symfony\View\View
 */
abstract class AbstractViewTest extends TestCase
{
    abstract protected function createViewUnderTest($template = null, array $parameters = [], $viewType = 'full'): View;

    /**
     * Returns parameters that are always returned by this view.
     *
     * @return array
     */
    protected function getAlwaysAvailableParams(): array
    {
        return [];
    }

    public function testGetSetParameters(): void
    {
        $params = [
            'bar' => 'baz',
            'fruit' => 'apple',
        ];

        $view = $this->createViewUnderTest('foo');
        $view->setParameters($params);

        self::assertSame($this->getAlwaysAvailableParams() + $params, $view->getParameters());
    }

    public function testAddParameters(): void
    {
        $params = ['bar' => 'baz', 'fruit' => 'apple'];
        $view = $this->createViewUnderTest('foo', $params);

        $additionalParams = ['truc' => 'muche', 'laurel' => 'hardy'];
        $view->addParameters($additionalParams);

        $this->assertSame($this->getAlwaysAvailableParams() + $params + $additionalParams, $view->getParameters());
    }

    public function testHasParameter(): View
    {
        $view = $this->createViewUnderTest(__METHOD__, ['foo' => 'bar']);

        $this->assertTrue($view->hasParameter('foo'));
        $this->assertFalse($view->hasParameter('nonExistent'));

        return $view;
    }

    /**
     * @depends testHasParameter
     */
    public function testGetParameter(View $view): View
    {
        $this->assertSame('bar', $view->getParameter('foo'));

        return $view;
    }

    /**
     * @depends testGetParameter
     */
    public function testGetParameterFail(View $view): void
    {
        $this->expectException(InvalidArgumentException::class);

        $view->getParameter('nonExistent');
    }

    /**
     * @dataProvider goodTemplateIdentifierProvider
     *
     * @param string|callable $templateIdentifier
     */
    public function testSetTemplateIdentifier($templateIdentifier): void
    {
        $contentView = $this->createViewUnderTest();
        $contentView->setTemplateIdentifier($templateIdentifier);

        $this->assertSame($templateIdentifier, $contentView->getTemplateIdentifier());
    }

    public function goodTemplateIdentifierProvider(): array
    {
        return [
            ['foo:bar:baz.html.twig'],
            [
                static function () {
                    return 'foo';
                },
            ],
        ];
    }

    /**
     * @dataProvider badTemplateIdentifierProvider
     *
     * @param mixed $badTemplateIdentifier
     */
    public function testSetTemplateIdentifierWrongType($badTemplateIdentifier): void
    {
        $this->expectException(InvalidArgumentType::class);

        $contentView = $this->createViewUnderTest();
        $contentView->setTemplateIdentifier($badTemplateIdentifier);
    }

    public function badTemplateIdentifierProvider(): array
    {
        return [
            [123],
            [true],
            [new \stdClass()],
            [['foo', 'bar']],
        ];
    }
}

class_alias(AbstractViewTest::class, 'eZ\Publish\Core\MVC\Symfony\View\Tests\AbstractViewTest');
