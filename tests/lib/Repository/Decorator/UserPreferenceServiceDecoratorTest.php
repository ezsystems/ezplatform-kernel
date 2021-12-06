<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\Decorator\UserPreferenceServiceDecorator;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserPreferenceServiceDecoratorTest extends TestCase
{
    protected function createDecorator(MockObject $service): UserPreferenceService
    {
        return new class($service) extends UserPreferenceServiceDecorator {
        };
    }

    protected function createServiceMock(): MockObject
    {
        return $this->createMock(UserPreferenceService::class);
    }

    public function testSetUserPreferenceDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [['random_value_5ced05ce1437c3.99987071']];

        $serviceMock->expects($this->once())->method('setUserPreference')->with(...$parameters);

        $decoratedService->setUserPreference(...$parameters);
    }

    public function testGetUserPreferenceDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = ['random_value_5ced05ce143830.91322594'];

        $serviceMock->expects($this->once())->method('getUserPreference')->with(...$parameters);

        $decoratedService->getUserPreference(...$parameters);
    }

    public function testLoadUserPreferencesDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            360,
            922,
        ];

        $serviceMock->expects($this->once())->method('loadUserPreferences')->with(...$parameters);

        $decoratedService->loadUserPreferences(...$parameters);
    }

    public function testGetUserPreferenceCountDecorator()
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [];

        $serviceMock->expects($this->once())->method('getUserPreferenceCount')->with(...$parameters);

        $decoratedService->getUserPreferenceCount(...$parameters);
    }
}

class_alias(UserPreferenceServiceDecoratorTest::class, 'eZ\Publish\SPI\Repository\Tests\Decorator\UserPreferenceServiceDecoratorTest');
