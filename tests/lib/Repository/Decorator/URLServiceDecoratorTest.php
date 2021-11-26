<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\URLServiceDecorator;
use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class URLServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): URLService
    {
        return new class($service) extends URLServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(URLService::class);
    }

    public function testCreateUpdateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('createUpdateStruct')->with(...$parameters);

        $decoratedService->createUpdateStruct(...$parameters);
    }

    public function testFindUrlsDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(URLQuery::class)];

        $serviceMock->expects($this->once())->method('findUrls')->with(...$parameters);

        $decoratedService->findUrls(...$parameters);
    }

    public function testFindUsagesDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(URL::class),
            10,
            100,
        ];

        $serviceMock->expects($this->once())->method('findUsages')->with(...$parameters);

        $decoratedService->findUsages(...$parameters);
    }

    public function testLoadByIdDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [1];

        $serviceMock->expects($this->once())->method('loadById')->with(...$parameters);

        $decoratedService->loadById(...$parameters);
    }

    public function testLoadByUrlDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce172635.77719845'];

        $serviceMock->expects($this->once())->method('loadByUrl')->with(...$parameters);

        $decoratedService->loadByUrl(...$parameters);
    }

    public function testUpdateUrlDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(URL::class),
            $this->createMock(URLUpdateStruct::class),
        ];

        $serviceMock->expects($this->once())->method('updateUrl')->with(...$parameters);

        $decoratedService->updateUrl(...$parameters);
    }
}

class_alias(URLServiceDecoratorTest::class, 'eZ\Publish\SPI\Repository\Tests\Decorator\URLServiceDecoratorTest');
