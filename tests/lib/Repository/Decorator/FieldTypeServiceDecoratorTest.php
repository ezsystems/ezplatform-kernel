<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\FieldTypeServiceDecorator;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldTypeServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): FieldTypeService
    {
        return new class($service) extends FieldTypeServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(FieldTypeService::class);
    }

    public function testGetFieldTypesDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('getFieldTypes')->with(...$parameters);

        $decoratedService->getFieldTypes(...$parameters);
    }

    public function testGetFieldTypeDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce0eda66.08473991'];

        $serviceMock->expects($this->once())->method('getFieldType')->with(...$parameters);

        $decoratedService->getFieldType(...$parameters);
    }

    public function testHasFieldTypeDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce0edab1.24451920'];

        $serviceMock->expects($this->once())->method('hasFieldType')->with(...$parameters);

        $decoratedService->hasFieldType(...$parameters);
    }
}

class_alias(FieldTypeServiceDecoratorTest::class, 'eZ\Publish\SPI\Repository\Tests\Decorator\FieldTypeServiceDecoratorTest');
