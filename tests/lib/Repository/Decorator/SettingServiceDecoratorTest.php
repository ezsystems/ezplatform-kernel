<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\SettingServiceDecorator;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SettingServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_SETTING_GROUP = 'group_a1';
    private const EXAMPLE_SETTING_IDENTIFIER = 'setting_b2';

    protected function createDecorator(MockObject $service): SettingService
    {
        return new class($service) extends SettingServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(SettingService::class);
    }

    public function testCreateSettingDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(SettingCreateStruct::class)];

        $serviceMock->expects($this->once())->method('createSetting')->with(...$parameters);

        $decoratedService->createSetting(...$parameters);
    }

    public function testUpdateSettingDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(Setting::class),
            $this->createMock(SettingUpdateStruct::class),
        ];

        $serviceMock->expects($this->once())->method('updateSetting')->with(...$parameters);

        $decoratedService->updateSetting(...$parameters);
    }

    public function testLoadSettingDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            self::EXAMPLE_SETTING_GROUP,
            self::EXAMPLE_SETTING_IDENTIFIER,
        ];

        $serviceMock->expects($this->once())->method('loadSetting')->with(...$parameters);

        $decoratedService->loadSetting(...$parameters);
    }

    public function testDeleteSettingDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Setting::class)];

        $serviceMock->expects($this->once())->method('deleteSetting')->with(...$parameters);

        $decoratedService->deleteSetting(...$parameters);
    }

    public function testNewSettingCreateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('newSettingCreateStruct')->with(...$parameters);

        $decoratedService->newSettingCreateStruct(...$parameters);
    }

    public function testNewSettingUpdateStructDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('newSettingUpdateStruct')->with(...$parameters);

        $decoratedService->newSettingUpdateStruct(...$parameters);
    }
}

class_alias(SettingServiceDecoratorTest::class, 'eZ\Publish\SPI\Repository\Tests\Decorator\SettingServiceDecoratorTest');
