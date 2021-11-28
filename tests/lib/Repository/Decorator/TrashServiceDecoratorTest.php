<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\TrashServiceDecorator;
use Ibexa\Contracts\Core\Repository\TrashService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TrashServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): TrashService
    {
        return new class($service) extends TrashServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(TrashService::class);
    }

    public function testLoadTrashItemDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [1];

        $serviceMock->expects($this->once())->method('loadTrashItem')->with(...$parameters);

        $decoratedService->loadTrashItem(...$parameters);
    }

    public function testTrashDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Location::class)];

        $serviceMock->expects($this->once())->method('trash')->with(...$parameters);

        $decoratedService->trash(...$parameters);
    }

    public function testRecoverDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(TrashItem::class),
            $this->createMock(Location::class),
        ];

        $serviceMock->expects($this->once())->method('recover')->with(...$parameters);

        $decoratedService->recover(...$parameters);
    }

    public function testEmptyTrashDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('emptyTrash')->with(...$parameters);

        $decoratedService->emptyTrash(...$parameters);
    }

    public function testDeleteTrashItemDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(TrashItem::class)];

        $serviceMock->expects($this->once())->method('deleteTrashItem')->with(...$parameters);

        $decoratedService->deleteTrashItem(...$parameters);
    }

    public function testFindTrashItemsDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Query::class)];

        $serviceMock->expects($this->once())->method('findTrashItems')->with(...$parameters);

        $decoratedService->findTrashItems(...$parameters);
    }
}

class_alias(TrashServiceDecoratorTest::class, 'eZ\Publish\SPI\Repository\Tests\Decorator\TrashServiceDecoratorTest');
